<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Guarantee;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class RentalService
{
    public function updatePayment(Rental $rental, \App\Models\Payment $payment, array $data): void
    {
        app(\App\Services\PaymentService::class)->update($rental, $payment, $data);
        $rental->refresh();
        $this->recalculatePaymentStatus($rental);
    }

    public function deletePayment(Rental $rental, \App\Models\Payment $payment): void
    {
        app(\App\Services\PaymentService::class)->delete($rental, $payment);
        $rental->refresh();
        $this->recalculatePaymentStatus($rental);
    }

    public function refundPayment(Rental $rental, \App\Models\Payment $payment): void
    {
        app(\App\Services\PaymentService::class)->refundPayment($rental, $payment);
        $rental->refresh();
        $this->recalculatePaymentStatus($rental);
    }

    public function voidPayment(Rental $rental, \App\Models\Payment $payment): void
    {
        app(\App\Services\PaymentService::class)->voidPayment($rental, $payment);
        $rental->refresh();
        $this->recalculatePaymentStatus($rental);
    }

    public function recalculatePaymentStatus(Rental $rental): void
    {
        $totalPaid = (float) $rental->payments()->sum('amount');
        $paidAmount = $totalPaid;
        $remainingAmount = (float) max(0, ((float) $rental->total_amount) - $paidAmount);

        if ($paidAmount <= 0) {
            $paymentStatus = Rental::PAYMENT_UNPAID;
        } elseif ($paidAmount < (float) $rental->total_amount) {
            $paymentStatus = Rental::PAYMENT_PARTIAL;
        } else {
            $paymentStatus = Rental::PAYMENT_PAID;
        }

        $rental->update([
            'paid_amount' => $paidAmount,
            'payment_status' => $paymentStatus,
            'remaining_amount' => $remainingAmount,
        ]);

        $this->logActivity('recalculate_payment_status', $rental, "Recalculate payment status untuk {$rental->invoice_number}");
    }

    public function buildDetailPayload(Rental $rental): array
    {
        return [
            'id' => $rental->id,
            'invoice_number' => $rental->invoice_number,
            'rental_status' => $rental->rental_status,
            'payment_status' => $rental->payment_status,
            'rental_date' => $rental->rental_date?->format('d/m/Y') ?? '-',
            'return_due_date' => $rental->return_due_date?->format('d/m/Y') ?? '-',
            'created_by' => optional($rental->createdBy)->name,
            'subtotal' => (float) ($rental->subtotal ?? 0),
            'discount' => (float) ($rental->discount ?? 0),
            'deposit' => (float) ($rental->deposit ?? 0),
            'total_amount' => (float) ($rental->total_amount ?? 0),
            'paid_amount' => (float) ($rental->paid_amount ?? 0),
            'remaining_amount' => (float) ($rental->remaining_amount ?? 0),
            'late_fee' => (float) ($rental->late_fee ?? 0),
            'payment_method' => $rental->payment_method ?? null,
            'customer' => [
                'name' => $rental->customer->name ?? '-',
                'phone' => $rental->customer->phone ?? '-',
            ],
            'branch' => [
                'name' => $rental->branch->name ?? '-',
            ],
            'items' => $rental->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'size' => $item->product_size,
                    'category_name' => optional($item->product?->category)->name ?? '-',
                    'quantity' => (int) $item->quantity,
                    'price' => (float) ($item->price_per_day ?? 0),
                    'subtotal' => (float) ($item->subtotal ?? (($item->price_per_day ?? 0) * ($item->quantity ?? 0))),
                    'photo' => $item->product && $item->product->photo ? asset('storage/' . $item->product->photo) : null,
                    'return_condition' => $item->return_condition,
                    'damage_fee' => (float) ($item->damage_fee ?? 0),
                    'return_notes' => $item->return_notes,
                    'is_returned' => (bool) $item->is_returned,
                ];
            })->values(),
            'guarantees' => $rental->guarantees->map(function ($guarantee) {
                return [
                    'id' => $guarantee->id,
                    'type' => $guarantee->type,
                    'type_label' => $guarantee->type_label ?? $guarantee->type,
                    'id_number' => $guarantee->id_number,
                    'deposit_amount' => (float) ($guarantee->deposit_amount ?? 0),
                    'status' => $guarantee->status,
                    'notes' => $guarantee->description ?? null,
                    'id_photo_url' => $guarantee->id_photo_url ?? null,
                ];
            })->values(),
            'payments' => $rental->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'payment_number' => $payment->payment_number,
                    'amount' => (float) ($payment->amount ?? 0),
                    'method' => $payment->method,
                    'method_label' => $payment->method_label,
                    'paid_at' => $payment->paid_at?->format('d/m/Y H:i'),
                    'notes' => $payment->notes,
                ];
            })->values(),
            'activity_logs' => $rental->activityLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'user' => optional($log->user)->name,
                    'created_at' => $log->created_at?->format('d/m/Y H:i'),
                ];
            })->values(),
        ];
    }

    /**
     * Generate a globally unique, branch-aware, chronological invoice number.
     *
     * Format: INV{YYYYMMDD}{BB}{SSSS}
     * Example: INV20260727010001
     *
     * Race-condition safe:
     * - Uses lockForUpdate() on the last matching invoice
     * - Uses withTrashed() to never reuse numbers from soft-deleted rentals
     * - Retry loop with up to 100 attempts per branch+date
     * - Final EXISTS check before returning
     *
     * Must be called from within a DB::transaction() with the same connection.
     *
     * @param int $branchId
     * @return string
     * @throws \RuntimeException after 100 failed attempts
     */
    public function generateInvoiceNumber(int $branchId): string
    {
        $prefix = 'INV';
        $now = now();
        $date = $now->format('Ymd');
        $branchCode = str_pad($branchId, 2, '0', STR_PAD_LEFT);
        $pattern = "{$prefix}{$date}{$branchCode}%";
        $maxAttempts = 100;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            // 1. Lock the LAST matching invoice row for today + branch
            //    withTrashed() ensures soft-deleted invoice numbers are NEVER reused.
            $last = Rental::withTrashed()
                ->where('invoice_number', 'like', $pattern)
                ->whereDate('created_at', $now->toDateString())
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            // 2. Extract or start sequence
            $nextSequence = $last
                ? (int) substr($last->invoice_number, -4) + 1
                : 1;

            // 3. Generate candidate number
            $candidate = $prefix . $date . $branchCode . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

            // 4. Verify uniqueness with EXISTS check
            //    (pessimistic lock above prevents concurrent inserts,
            //     but this double-check ensures correctness)
            $exists = Rental::withTrashed()
                ->where('invoice_number', $candidate)
                ->lockForUpdate()
                ->exists();

            if (!$exists) {
                return $candidate;
            }

            // 5. Collision detected — retry with next sequence
            //    This can happen if a concurrent transaction inserted
            //    the same number between our lock and check.
            //    We simply loop and try the next sequence number.
        }

        // 6. Max retries exhausted — fail hard instead of creating a duplicate
        throw new \RuntimeException(
            "Gagal membuat nomor invoice setelah {$maxAttempts} percobaan " .
            "untuk cabang {$branchId} pada tanggal {$date}. " .
            "Silakan coba lagi."
        );
    }

    public function generatePaymentNumber(): string
    {
        $prefix = 'PAY';
        $now = now();
        $date = $now->format('Ymd');
        $last = Payment::where('payment_number', 'like', "{$prefix}{$date}%")
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();
        $sequence = $last ? (int) substr($last->payment_number, -4) + 1 : 1;
        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function createRental(array $data): Rental
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $durationDays = (int) $data['duration_days'];

            // ─── STEP 1: Collect branch IDs from all selected products (with lock) ───
            $branchIds = collect($data['items'])
                ->pluck('product_id')
                ->map(fn($id) => Product::whereKey($id)->lockForUpdate()->valueOrFail('branch_id'))
                ->unique();

            // ─── STEP 2: Reject mixed-branch rentals ───
            if ($branchIds->count() > 1) {
                throw ValidationException::withMessages([
                    'items' => 'Semua produk harus berasal dari cabang yang sama.',
                ]);
            }

            // ─── STEP 3: Resolve rental branch ───
            $branchId = $user->branch_id ?? $branchIds->first();

            if ($branchId === null) {
                throw ValidationException::withMessages([
                    'items' => 'Tidak dapat menentukan cabang. Silakan pilih produk terlebih dahulu.',
                ]);
            }

            // ─── STEP 4: Verify non-super-admin users own the branch ───
            if (!$user->isSuperAdmin() && (int) $user->branch_id !== (int) $branchId) {
                throw ValidationException::withMessages([
                    'items' => 'Produk bukan berasal dari cabang Anda.',
                ]);
            }

            // Calculate subtotal (using locked products from step 1)
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $subtotal += $product->rental_price * $item['quantity'] * $durationDays;
            }

            $discount = $data['discount'] ?? 0;
            $totalAmount = $subtotal - $discount;

            // ─── Generate invoice number inside the same transaction ───
            $invoiceNumber = $this->generateInvoiceNumber($branchId);

            // Create rental with resolved branchId
            $rental = Rental::create([
                'invoice_number'  => $invoiceNumber,
                'branch_id'       => $branchId,
                'customer_id'     => $data['customer_id'],
                'created_by'      => $user->id,
                'rental_date'     => $data['rental_date'],
                'return_due_date' => Carbon::parse($data['rental_date'])->addDays($durationDays),
                'duration_days'   => $durationDays,
                'subtotal'        => $subtotal,
                'discount'        => $discount,
                'total_amount'    => $totalAmount,
                'paid_amount'     => 0,
                'payment_status'  => Rental::PAYMENT_UNPAID,
                'payment_method'  => $data['payment_method'] ?? null,
                'rental_status'   => Rental::STATUS_WAITING,
                'notes'           => $data['notes'] ?? null,
            ]);

            // Create rental items & reduce stock
            foreach ($data['items'] as $item) {
                $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $itemSubtotal = $product->rental_price * $item['quantity'] * $durationDays;

                RentalItem::create([
                    'rental_id'     => $rental->id,
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_size'  => $item['product_size'],
                    'product_color' => $product->color,
                    'quantity'      => $item['quantity'],
                    'price_per_day' => $product->rental_price,
                    'duration_days' => $durationDays,
                    'subtotal'      => $itemSubtotal,
                ]);

                // Reduce stock (locked row guaranteed fresh)
                $newStock = $product->stock_available - $item['quantity'];
                if ($newStock < 0) {
                    throw ValidationException::withMessages([
                        'items' => "Stok produk {$product->name} tidak mencukupi (tersedia: {$product->stock_available}, diminta: {$item['quantity']}).",
                    ]);
                }
                $product->update([
                    'stock_available' => $newStock,
                    'status'          => $newStock <= 0 ? 'rented' : $product->status,
                ]);
            }

            // Create guarantee - handle flat form fields
            $guaranteeType = $data['guarantee_type'] ?? ($data['guarantee']['type'] ?? null);
            if (!empty($guaranteeType)) {
                $idPhotoPath = null;
                if (!empty($data['guarantee_id_photo']) && is_a($data['guarantee_id_photo'], \Illuminate\Http\UploadedFile::class)) {
                    $idPhotoPath = $data['guarantee_id_photo']->store('guarantees/id_photos', 'public');
                }

                Guarantee::create([
                    'rental_id'      => $rental->id,
                    'type'           => $guaranteeType,
                    'id_number'      => $data['guarantee_id_number'] ?? ($data['guarantee']['id_number'] ?? null),
                    'id_name'        => $data['guarantee_id_name'] ?? ($data['guarantee']['id_name'] ?? null),
                    'deposit_amount' => $data['guarantee_deposit'] ?? ($data['guarantee']['deposit_amount'] ?? 0),
                    'description'    => $data['guarantee_notes'] ?? ($data['guarantee']['description'] ?? null),
                    'id_photo'       => $idPhotoPath,
                    'status'         => 'held',
                ]);
            }

            // Generate QR code
            $this->generateQrCode($rental);

            // Log activity
            $this->logActivity('create_rental', $rental, "Membuat penyewaan {$rental->invoice_number}");

            return $rental->fresh(['items', 'customer', 'guarantees']);
        });
    }

    public function processPayment(Rental $rental, array $data): Payment
    {
        return DB::transaction(function () use ($rental, $data) {
            $amount = $data['amount'] ?? 0;
            if (is_string($amount)) {
                $amount = preg_replace('/[^0-9]/', '', $amount);
            }
            $amount = (float) $amount;
            $now = now();

            $payment = Payment::create([
                'rental_id'        => $rental->id,
                'received_by'      => Auth::id(),
                'payment_number'   => $this->generatePaymentNumber(),
                'amount'           => $amount,
                'method'           => $data['method'],
                'reference_number' => $data['reference_number'] ?? null,
                'type'             => $data['type'] ?? 'rental',
                'notes'            => $data['notes'] ?? null,
                'paid_at'          => $now,
            ]);

            $newPaidAmount = $rental->paid_amount + $amount;
            $rental->update([
                'paid_amount'    => $newPaidAmount,
                'payment_status' => $newPaidAmount >= $rental->total_amount ? 'paid' : 'partial',
                'rental_status'  => $newPaidAmount >= $rental->total_amount
                    ? Rental::STATUS_ACTIVE
                    : $rental->rental_status,
            ]);

            $this->logActivity('process_payment', $rental, "Pembayaran {$payment->payment_number} sebesar Rp " . number_format($data['amount'], 0, ',', '.'));

            return $payment;
        });
    }

    public function processReturn(Rental $rental, array $data = []): Rental
    {
        return DB::transaction(function () use ($rental, $data) {
            $now = now();
            $lateFee = 0;
            $totalDamageFees = 0;

            if ($rental->return_due_date->lt($now)) {
                $overdueDays = $rental->return_due_date->diffInDays($now);
                $dailyRate = $rental->subtotal / $rental->duration_days;
                $lateFee = $dailyRate * $overdueDays * 0.5;
            }

            foreach ($rental->items as $item) {
                $returnData = collect($data['items'] ?? [])
                    ->firstWhere('rental_item_id', $item->id);

                $condition = $returnData['condition'] ?? 'baik';
                $damageFee = $returnData['damage_fee'] ?? 0;
                $totalDamageFees += $damageFee;

                $item->update([
                    'is_returned'      => true,
                    'returned_at'      => $now,
                    'return_condition' => $condition,
                    'damage_fee'       => $damageFee,
                    'return_notes'     => $returnData['notes'] ?? null,
                ]);

                $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                $newStock = $product->stock_available + $item->quantity;
                $product->update([
                    'stock_available' => $newStock,
                    'status' => ($product->status === 'rented' && $newStock > 0) ? 'available' : $product->status,
                ]);
            }

            $rental->guarantees()->update(['status' => 'returned', 'returned_at' => $now]);

            $totalWithFees = $rental->total_amount + $lateFee + $totalDamageFees;
            $rental->update([
                'rental_status'      => Rental::STATUS_RETURNED,
                'actual_return_date' => $now->toDateString(),
                'returned_at'        => $now,
                'late_fee'           => $lateFee,
                'total_amount'       => $totalWithFees,
            ]);

            $this->logActivity('return_rental', $rental, "Pengembalian {$rental->invoice_number}");

            return $rental->fresh();
        });
    }

    public function generateQrCode(Rental $rental): void
    {
        $qrData = route('rentals.scan', $rental->invoice_number);
        $path = 'qrcodes/rentals/' . $rental->invoice_number . '.svg';
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $svg = QrCode::format('svg')->size(200)->generate($qrData);
        file_put_contents($fullPath, $svg);

        $rental->update(['qr_code' => $path]);
    }

    public function getQrUrl(Rental $rental): string
    {
        return route('rentals.scan', $rental->invoice_number);
    }

    public function updateOverdueRentals(): int
    {
        return Rental::where('rental_status', Rental::STATUS_ACTIVE)
            ->where('return_due_date', '<', now()->toDateString())
            ->update(['rental_status' => Rental::STATUS_OVERDUE]);
    }

    protected function logActivity(string $action, Rental $rental, string $description): void
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'branch_id'   => Auth::user()?->branch_id,
            'action'      => $action,
            'model_type'  => Rental::class,
            'model_id'    => $rental->id,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
