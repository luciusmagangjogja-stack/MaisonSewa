<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceService
{
    public function list(Request $request): array
    {
        $user = Auth::user();

        $query = Rental::query()->with(['customer', 'branch', 'payments']);

        if ($user->role === 'sales') {
            $query->where('created_by', $user->id);
        } elseif (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c
                      ->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->latest('rental_date')->paginate(15)->withQueryString();

        return ['invoices' => $invoices];
    }

    public function create(array $data): Rental
    {
        throw new \BadMethodCallException('Invoice create not implemented; use rentals.create for creating a rental/invoice.');
    }

    public function buildPayload(Rental $invoice): array
    {
        $invoice->loadMissing(['customer', 'branch', 'items', 'payments', 'createdBy']);

        $paid = (float) $invoice->payments()->sum('amount');

        return [
            'paid_amount' => $paid,
            'remaining_amount' => max(0, (float) $invoice->total_amount - $paid),
        ];
    }

    public function buildReceiptPayload(Rental $invoice): array
    {
        $invoice->loadMissing(['payments', 'guarantees', 'items.product.category']);

        $deposit = (float) ($invoice->guarantees->where('type', 'deposit')->sum('deposit_amount') ?? 0);
        $lateFee = (float) ($invoice->late_fee ?? 0);
        $damageFee = (float) ($invoice->items->sum('damage_fee') ?? 0);
        $denda = $lateFee + $damageFee;

        $paidAmount = (float) ($invoice->paid_amount ?? 0);

        return [
            'receipt_number' => 'RCPT-' . $invoice->invoice_number,
            'deposit' => $deposit,
            'late_fee' => $lateFee,
            'damage_fee' => $damageFee,
            'denda' => $denda,
            'paid_amount' => $paidAmount,
            'total_amount' => (float) ($invoice->total_amount ?? 0),
            'remaining_amount' => max(0, (float) ($invoice->total_amount ?? 0) - $paidAmount),
        ];
    }

    public function update(Rental $invoice, array $data): void
    {
        $old = $invoice->only(['notes']);

        $invoice->update([
            'notes' => $data['notes'] ?? $invoice->notes,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'branch_id' => Auth::user()?->branch_id,
            'action' => 'update_invoice',
            'model_type' => Rental::class,
            'model_id' => $invoice->id,
            'description' => Auth::user()->name . ' mengubah invoice',
            'old_values' => $old,
            'new_values' => ['notes' => $invoice->notes],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Cancel an invoice (legal cancel, not delete).
     * Only invoices with waiting/active/overdue status can be cancelled.
     * Restores stock for all items.
     * Cancelled invoices remain in database permanently.
     */
    public function cancel(Rental $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            if (!in_array($invoice->rental_status, ['waiting', 'active', 'overdue'], true)) {
                abort(422, 'Invoice tidak bisa dibatalkan karena status sudah selesai/dikembalikan.');
            }

            // Restore stock for items that have not yet been returned.
            // If an item was already returned, its stock was already restored
            // during processReturn/updateStatus, so we must NOT restore it again here.
            foreach ($invoice->items as $item) {
                if ($item->is_returned) {
                    continue;
                }

                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->stock_available = (int)$product->stock_available + (int)$item->quantity;
                    $product->status = ($product->status === 'rented' && $product->stock_available > 0) ? 'available' : $product->status;
                    $product->save();
                }
            }

            // Mark rental as cancelled
            $oldStatus = $invoice->rental_status;
            $now = now();
            $invoice->update([
                'rental_status' => 'cancelled',
                'cancelled_at' => $now,
                'actual_return_date' => $now->toDateString(),
            ]);

            // Audit log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'branch_id' => Auth::user()?->branch_id,
                'action' => 'cancel_invoice',
                'model_type' => Rental::class,
                'model_id' => $invoice->id,
                'description' => Auth::user()->name . ' membatalkan invoice ' . $invoice->invoice_number,
                'old_values' => ['rental_status' => $oldStatus],
                'new_values' => ['rental_status' => 'cancelled'],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }

    public function downloadPdf(Rental $invoice)
    {
        $invoice->loadMissing(['customer', 'branch', 'items', 'payments', 'createdBy']);
        $payload = $this->buildPayload($invoice);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'payload'));
        return $pdf->download(($invoice->invoice_number ?? 'INV') . '.pdf');
    }

    public function qr(Rental $invoice)
    {
        $qrData = route('invoices.show', $invoice);
        $svg = QrCode::format('svg')->size(200)->generate($qrData);
        return response($svg)->header('Content-Type', 'image/svg+xml');
    }

    public function whatsapp(Rental $invoice)
    {
        $message = urlencode(
            "Halo {$invoice->customer->name},\n\n" .
            "Berikut adalah Invoice Anda.\n\n" .
            "Nomor Invoice: {$invoice->invoice_number}\n\n" .
            "Silakan download PDF di tautan berikut:\n" .
            route('invoices.pdf', $invoice)
        );

        $waUrl = "https://wa.me/{$invoice->customer->phone}?text={$message}";
        return redirect()->away($waUrl);
    }
}
