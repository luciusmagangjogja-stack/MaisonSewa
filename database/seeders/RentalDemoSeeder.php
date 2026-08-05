<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\Guarantee;
use App\Models\Payment;
use Carbon\Carbon;

class RentalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $customers = Customer::all();
        $users = User::all();
        $products = Product::all();

        $statuses = ['active', 'returned', 'overdue'];
        $paymentStatuses = ['paid', 'partial', 'unpaid'];

        // Buat 20 transaksi sewa
        for ($i = 1; $i <= 20; $i++) {
            $branch = $branches->random();
            $customer = $customers->random();
            $user = $users->where('branch_id', $branch->id)->random();

            $rentalDate = Carbon::now()->subDays(rand(1, 60));
            $durationDays = rand(1, 7);
            $returnDueDate = $rentalDate->copy()->addDays($durationDays);

            $rentalStatus = $this->getRandomRentalStatus();
            $actualReturnDate = null;
            $lateFee = 0;

            if ($rentalStatus === 'returned') {
                $actualReturnDate = $returnDueDate->copy()->addDays(rand(0, 2));
                if ($actualReturnDate > $returnDueDate) {
                    $lateDays = $actualReturnDate->diffInDays($returnDueDate);
                    $lateFee = $lateDays * 10000;
                }
            } elseif ($rentalStatus === 'overdue') {
                $actualReturnDate = null;
                $lateDays = Carbon::now()->diffInDays($returnDueDate);
                $lateFee = $lateDays * 10000;
            }

            $invoiceNumber = 'RENT/' . $branch->code . '/' . date('Y') . '/' . str_pad($i, 5, '0', STR_PAD_LEFT);

            $rental = Rental::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'created_by' => $user->id,
                'rental_date' => $rentalDate,
                'return_due_date' => $returnDueDate,
                'actual_return_date' => $actualReturnDate,
                'duration_days' => $durationDays,
                'subtotal' => 0,
                'discount' => rand(0, 1) === 1 ? rand(5000, 25000) : 0,
                'late_fee' => $lateFee,
                'total_amount' => 0,
                'paid_amount' => 0,
                'change_amount' => 0,
                'payment_status' => $rentalStatus === 'returned' ? 'paid' : $this->getRandomPaymentStatus(),
                'rental_status' => $rentalStatus,
                'qr_code' => 'QR-' . strtoupper(uniqid()),
                'notes' => $this->getRandomNotes(),
                'returned_at' => $actualReturnDate ? $actualReturnDate->toDateTimeString() : null,
                'created_at' => $rentalDate,
                'updated_at' => $rentalDate,
            ]);

            // Tambah produk item ke rental
            $numItems = rand(1, 3);
            $selectedProducts = $products->random($numItems);
            $subtotal = 0;

            foreach ($selectedProducts as $product) {
                $itemSubtotal = $product->rental_price * $durationDays;
                $subtotal += $itemSubtotal;

                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_size' => $product->size,
                    'product_color' => $product->color,
                    'quantity' => 1,
                    'price_per_day' => $product->rental_price,
                    'duration_days' => $durationDays,
                    'subtotal' => $itemSubtotal,
                    'return_condition' => $rentalStatus === 'returned' ? ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'][rand(0, 3)] : null,
                    'damage_fee' => $rentalStatus === 'returned' && rand(0, 4) === 0 ? rand(25000, 100000) : 0,
                    'is_returned' => $rentalStatus === 'returned',
                    'returned_at' => $actualReturnDate ? $actualReturnDate->toDateTimeString() : null,
                    'created_at' => $rentalDate,
                    'updated_at' => $rentalDate,
                ]);
            }

            // Hitung total
            $totalAmount = $subtotal - $rental->discount + $lateFee;
            $paidAmount = $rental->payment_status === 'paid' ? $totalAmount : ($rental->payment_status === 'partial' ? $totalAmount * 0.5 : 0);

            $rental->update([
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'change_amount' => max(0, $paidAmount - $totalAmount),
            ]);

            // Buat jaminan
            Guarantee::create([
                'rental_id' => $rental->id,
                'type' => ['ktp', 'sim', 'deposit'][rand(0, 2)],
                'id_number' => rand(1000000000, 9999999999),
                'id_name' => $customer->name,
                'deposit_amount' => rand(50000, 200000),
                'status' => $rentalStatus === 'returned' ? 'returned' : 'held',
                'returned_at' => $actualReturnDate ? $actualReturnDate->toDateTimeString() : null,
                'created_at' => $rentalDate,
                'updated_at' => $rentalDate,
            ]);

            // Buat pembayaran
            if ($paidAmount > 0) {
                Payment::create([
                    'rental_id' => $rental->id,
                    'received_by' => $user->id,
                    'payment_number' => 'PAY/' . $branch->code . '/' . date('Y') . '/' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'amount' => $paidAmount,
                    'method' => ['cash', 'transfer', 'qris'][rand(0, 2)],
                    'type' => 'rental',
                    'paid_at' => $rentalDate,
                    'created_at' => $rentalDate,
                    'updated_at' => $rentalDate,
                ]);
            }
        }

        $this->command->newLine();
        $this->command->info('✅ Data transaksi sewa DEMO berhasil ditambahkan!');
        $this->command->info('📊 Total: 20 transaksi sewa dengan berbagai status');
    }

    private function getRandomRentalStatus(): string
    {
        $statuses = ['active', 'returned', 'returned', 'returned', 'overdue'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomPaymentStatus(): string
    {
        $statuses = ['paid', 'paid', 'paid', 'partial'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Untuk acara pernikahan',
            'Untuk wisuda',
            'Untuk acara pesta',
            'Untuk meeting penting',
            'Untuk foto pre-wedding',
            'Untuk acara seminar',
            null,
            null,
            null,
        ];
        return $notes[array_rand($notes)];
    }
}
