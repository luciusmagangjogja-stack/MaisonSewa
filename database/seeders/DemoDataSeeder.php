<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\RentalItem;
use App\Models\RentalReturn;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Step 1: Update Andi's branch (branch 2) products to be different from Budi's
        $this->updateAndiProducts();

        // Step 2: Add some additional customers for both branches
        $this->addCustomers();

        // Step3: Add realistic rental data for both branches
        $this->addRentals();
    }

    private function updateAndiProducts(): void
    {
        $branchId = 2;
        $existingProducts = Product::where('branch_id', $branchId)->orderBy('id')->get();

        $newProductData = [
            [
                'category_id' => 3, // Jas Wisuda
                'name' => 'Jas Wisuda Hitam Modern',
                'size' => 'M',
                'color' => 'Hitam',
                'brand' => 'Zara',
                'rental_price' => 150000,
                'deposit_price' => 300000,
                'stock_total' => 3,
                'condition' => 'excellent',
                'status' => 'available',
            ],
            [
                'category_id' => 4, // Kebaya
                'name' => 'Kebaya Biru Muda',
                'size' => 'L',
                'color' => 'Biru Muda',
                'brand' => 'Batikk',
                'rental_price' => 120000,
                'deposit_price' => 240000,
                'stock_total' => 2,
                'condition' => 'good',
                'status' => 'available',
            ],
            [
                'category_id' => 5, // Rompi
                'name' => 'Rompi Kulit Coklat',
                'size' => 'XL',
                'color' => 'Coklat',
                'brand' => 'Louis',
                'rental_price' => 80000,
                'deposit_price' => 160000,
                'stock_total' => 4,
                'condition' => 'excellent',
                'status' => 'available',
            ],
        ];

        foreach ($existingProducts as $index => $product) {
            if (isset($newProductData[$index])) {
                $data = $newProductData[$index];
                $data['stock_available'] = $data['stock_total'];
                $product->update($data);
            }
        }

        // If there are more new products than existing, create them
        for ($i = count($existingProducts); $i < count($newProductData); $i++) {
            $productData = $newProductData[$i];
            $productData['branch_id'] = $branchId;
            $productData['stock_available'] = $productData['stock_total'];
            $productData['code'] = $this->generateProductCode($branchId);
            Product::create($productData);
        }
    }

    private function addCustomers(): void
    {
        $salesUserByBranch = [
            1 => 4, // Budi Santoso
            2 => 5, // Andi
        ];

        $customers = [
            ['name' => 'Dewi Lestari', 'phone' => '081234567890', 'branch_id' => 1],
            ['name' => 'Eko Prasetyo', 'phone' => '081234567891', 'branch_id' => 1],
            ['name' => 'Fitri Handayani', 'phone' => '081234567892', 'branch_id' => 2],
            ['name' => 'Galih Saputra', 'phone' => '081234567893', 'branch_id' => 2],
            ['name' => 'Hendra Kurniawan', 'phone' => '081234567894', 'branch_id' => 1],
            ['name' => 'Indah Permata', 'phone' => '081234567895', 'branch_id' => 2],
        ];

        foreach ($customers as $customerData) {
            $normalizedPhone = $this->normalizePhone($customerData['phone']);
            $existingCustomer = Customer::where('phone', $normalizedPhone)->first();
            if (!$existingCustomer) {
                $customerData['phone'] = $normalizedPhone;
                $customerData['user_id'] = $salesUserByBranch[$customerData['branch_id']];
                Customer::create($customerData);
            } else {
                // Update existing customer's user_id if null
                if (is_null($existingCustomer->user_id)) {
                    $existingCustomer->update(['user_id' => $salesUserByBranch[$existingCustomer->branch_id]]);
                }
            }
        }
    }

    private function addRentals(): void
    {
        // Get all customers and products
        $customersByBranch = [
            1 => Customer::where('branch_id', 1)->get(),
            2 => Customer::where('branch_id', 2)->get(),
        ];

        $productsByBranch = [
            1 => Product::where('branch_id', 1)->get(),
            2 => Product::where('branch_id', 2)->get(),
        ];

        // Sales user per branch
        $salesUserByBranch = [
            1 => 4, // Budi Santoso
            2 => 5, // Andi
        ];

        $rentalStatuses = ['waiting', 'active', 'overdue', 'returned'];
        $statusWeights = [0.1, 0.25, 0.05, 0.6]; // More returned, less overdue

        $daysToCover = 60;
        $totalRentals = 80;

        for ($i = 0; $i < $totalRentals; $i++) {
            $branchId = rand(1, 2);
            $customers = $customersByBranch[$branchId];
            $products = $productsByBranch[$branchId];

            if ($customers->isEmpty() || $products->isEmpty()) continue;

            // Generate random date in last $daysToCover days
            $daysAgo = rand(0, $daysToCover);
            $rentalDate = Carbon::now()->subDays($daysAgo)->setTime(rand(8, 18), rand(0, 59));
            $durationDays = rand(1, 7);
            $returnDueDate = $rentalDate->copy()->addDays($durationDays);

            $customer = $customers->random();
            $numItems = rand(1, 3);
            $selectedProducts = [];
            $subtotal = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $qty = rand(1, 2);
                $itemSubtotal = $product->rental_price * $qty * $durationDays;
                $subtotal += $itemSubtotal;
                $selectedProducts[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $itemSubtotal];
            }

            $discount = rand(0, 10) * 1000;
            $lateFee = 0;
            $totalAmount = $subtotal - $discount;
            $paidAmount = $totalAmount * rand(0, 100) / 100;
            $rentalStatus = $this->weightedRandom($rentalStatuses, $statusWeights);
            $paymentStatus = $paidAmount >= $totalAmount ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid');

            $invoiceNumber = 'INV' . $rentalDate->format('Ymd') . $branchId . str_pad($i + 10, 4, '0', STR_PAD_LEFT);

            $rental = Rental::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $branchId,
                'customer_id' => $customer->id,
                'created_by' => $salesUserByBranch[$branchId],
                'rental_date' => $rentalDate,
                'return_due_date' => $returnDueDate,
                'duration_days' => $durationDays,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'late_fee' => $lateFee,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
                'rental_status' => $rentalStatus,
                'created_at' => $rentalDate,
                'updated_at' => $rentalDate,
            ]);

            foreach ($selectedProducts as $itemData) {
                RentalItem::create([
                    'rental_id' => $rental->id,
                    'product_id' => $itemData['product']->id,
                    'product_name' => $itemData['product']->name,
                    'product_size' => $itemData['product']->size,
                    'product_color' => $itemData['product']->color,
                    'quantity' => $itemData['qty'],
                    'price_per_day' => $itemData['product']->rental_price,
                    'duration_days' => $durationDays,
                    'subtotal' => $itemData['subtotal'],
                ]);
            }

            // Create payment record
            if ($paidAmount > 0) {
                $paymentMethods = ['cash', 'transfer', 'qris'];
                $method = $paymentMethods[array_rand($paymentMethods)];
                $paymentNumber = 'PAY' . $rentalDate->format('Ymd') . $branchId . str_pad($i + 10, 4, '0', STR_PAD_LEFT);
                Payment::create([
                    'rental_id' => $rental->id,
                    'received_by' => $salesUserByBranch[$branchId],
                    'payment_number' => $paymentNumber,
                    'amount' => $paidAmount,
                    'method' => $method,
                    'type' => 'rental',
                    'notes' => 'Pembayaran sewa',
                    'paid_at' => $rentalDate->copy()->addMinutes(rand(10, 60)),
                    'created_at' => $rentalDate->copy()->addMinutes(rand(10, 60)),
                    'updated_at' => $rentalDate->copy()->addMinutes(rand(10, 60)),
                ]);
            }

            // If status is returned, create a RentalReturn
            if ($rentalStatus === 'returned') {
                $actualReturnDate = $returnDueDate->copy()->addDays(rand(0, 2));
                RentalReturn::create([
                    'rental_id' => $rental->id,
                    'returned_at' => $actualReturnDate,
                    'late_days' => max(0, $actualReturnDate->diffInDays($returnDueDate, false)),
                    'condition' => ['baik', 'kurang_baik', 'rusak_ringan'][array_rand(['baik', 'kurang_baik', 'rusak_ringan'])],
                    'return_notes' => 'Pengembalian barang',
                ]);
            }
        }
    }

    private function generateProductCode(int $branchId): string
    {
        $prefix = 'PRD' . str_pad($branchId, 2, '0', STR_PAD_LEFT);
        $last = Product::where('code', 'like', "{$prefix}%")->latest('id')->first();
        $seq = $last ? (int) substr($last->code, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private function normalizePhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (str_starts_with($digits, '620')) {
            $digits = '62' . substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif ($digits !== '' && !str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }
        return $digits;
    }

    private function weightedRandom(array $items, array $weights): mixed
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight * 100) / 100;
        $current = 0;

        foreach ($items as $index => $item) {
            $current += $weights[$index];
            if ($random <= $current) return $item;
        }
        return end($items);
    }
}
