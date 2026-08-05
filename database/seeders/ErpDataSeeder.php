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
use App\Models\Payment;
use App\Models\Guarantee;
use App\Models\RentalReturn;
use App\Models\ActivityLog;
use Carbon\Carbon;

class ErpDataSeeder extends Seeder
{
    private $branches;
    private $sales;
    private $categories;
    private $productsByBranch = [];
    private $customersBySales = [];

    public function run(): void
    {
        $this->command->info('Starting ERP Data Seeding...');

        $this->branches = Branch::all();
        $this->sales = User::where('role', 'sales')->get();
        $this->categories = Category::all();

        $this->createProducts();
        $this->createCustomers();
        $this->createRentalsAndRelated();

        $this->command->info('ERP Data Seeding Complete!');
    }

    private function createProducts(): void
    {
        $this->command->info('Creating Products...');

        $productNames = [
            'Jas Formal Hitam Classic', 'Jas Formal Navy Premium', 'Tuxedo Putih Elegant'
        ];

        $colors = ['Hitam', 'Navy', 'Abu'];
        $sizes = ['S', 'M', 'L'];
        $conditions = ['excellent', 'good'];
        $statuses = ['available', 'available', 'rented'];

        $productCodeCounter = 200;

        foreach ($this->branches as $branch) {
            $this->productsByBranch[$branch->id] = [];
            $categoryIndex = 0;

            foreach ($productNames as $name) {
                $category = $this->categories[$categoryIndex % count($this->categories)];
                $size = $sizes[array_rand($sizes)];
                $color = $colors[array_rand($colors)];
                $rentalPrice = rand(50000, 200000);
                $stockTotal = rand(2, 5);
                $status = $statuses[array_rand($statuses)];
                $stockAvailable = $status === 'available' ? rand(1, $stockTotal) : 0;

                $product = Product::create([
                    'branch_id' => $branch->id,
                    'category_id' => $category->id,
                    'code' => 'PRD' . str_pad($productCodeCounter++, 4, '0', STR_PAD_LEFT),
                    'name' => $name,
                    'size' => $size,
                    'color' => $color,
                    'brand' => 'Brand' . rand(1, 5),
                    'rental_price' => $rentalPrice,
                    'deposit_price' => $rentalPrice * 2,
                    'stock_total' => $stockTotal,
                    'stock_available' => $stockAvailable,
                    'condition' => $conditions[array_rand($conditions)],
                    'status' => $status,
                ]);

                $this->productsByBranch[$branch->id][] = $product;
            }
        }
        $this->command->info('Products Created: ' . Product::count());
    }

    private function createCustomers(): void
    {
        $this->command->info('Creating Customers...');

        $customerNames = [
            'Ahmad Fauzi', 'Budi Santoso', 'Citra Dewi'
        ];

        $counter = 1;

        foreach ($this->sales as $salesUser) {
            $this->customersBySales[$salesUser->id] = [];
            $numCustomers = 3;

            for ($i = 0; $i < $numCustomers; $i++) {
                if ($counter > count($customerNames)) {
                    $name = $customerNames[array_rand($customerNames)] . ' ' . rand(1, 99);
                } else {
                    $name = $customerNames[$counter - 1];
                }
                $counter++;

                $customer = Customer::create([
                    'branch_id' => $salesUser->branch_id,
                    'user_id' => $salesUser->id,
                    'name' => $name,
                    'phone' => '08' . rand(100000000, 999999999),
                    'email' => strtolower(str_replace(' ', '.', $name)) . rand(1, 999) . '@gmail.com',
                    'address' => 'Jl. Malioboro No. ' . rand(1, 100),
                    'is_blacklisted' => false,
                    'created_at' => Carbon::now()->subDays(rand(0, 29)),
                ]);

                $this->customersBySales[$salesUser->id][] = $customer;
            }
        }

        $this->command->info('Customers Created: ' . Customer::count());
    }

    private function createRentalsAndRelated(): void
    {
        $this->command->info('Creating Rentals & Related Data...');

        $rentalStatuses = ['waiting', 'active', 'returned'];
        $paymentStatuses = ['unpaid', 'partial', 'paid'];
        $paymentTypes = ['rental', 'deposit'];

        $totalRentals = 0;
        $totalPayments = 0;
        $totalReturns = 0;
        $totalGuarantees = 0;
        $totalActivities = 0;

        foreach ($this->sales as $salesUser) {
            $branchId = $salesUser->branch_id;
            $branchProducts = $this->productsByBranch[$branchId];
            $branchCustomers = $this->customersBySales[$salesUser->id];

            $numRentals = 3;

            for ($r = 0; $r < $numRentals; $r++) {
                $daysAgo = rand(0, 29);
                $rentalDate = Carbon::now()->subDays($daysAgo)->setTime(rand(8, 18), rand(0, 59));
                $durationDays = rand(1, 7);
                $returnDueDate = $rentalDate->copy()->addDays($durationDays);

                $customer = $branchCustomers[array_rand($branchCustomers)];
                $numItems = rand(1, 4);
                $selectedProducts = [];
                $subtotal = 0;

                for ($i = 0; $i < $numItems; $i++) {
                    $product = $branchProducts[array_rand($branchProducts)];
                    $qty = rand(1, 2);
                    $itemSubtotal = $product->rental_price * $qty * $durationDays;
                    $subtotal += $itemSubtotal;
                    $selectedProducts[] = ['product' => $product, 'qty' => $qty, 'subtotal' => $itemSubtotal];
                }

                $discount = rand(0, 10) * 1000;
                $lateFee = 0;
                $totalAmount = $subtotal - $discount;
                $paidAmount = 0;
                $rentalStatus = $rentalStatuses[array_rand($rentalStatuses)];
                $paymentStatus = 'unpaid';

                $invoiceNumber = 'INV' . $rentalDate->format('Ymd') . $branchId . str_pad($totalRentals + 1, 4, '0', STR_PAD_LEFT);

                $rental = Rental::create([
                    'invoice_number' => $invoiceNumber,
                    'branch_id' => $branchId,
                    'customer_id' => $customer->id,
                    'created_by' => $salesUser->id,
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
                ]);

                $totalRentals++;

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

                $activity = ActivityLog::create([
                    'user_id' => $salesUser->id,
                    'branch_id' => $branchId,
                    'action' => 'create_rental',
                    'model_type' => Rental::class,
                    'model_id' => $rental->id,
                    'description' => 'Membuat rental ' . $rental->invoice_number,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0',
                    'created_at' => $rentalDate,
                ]);
                $totalActivities++;

                $numPayments = rand(1, 3);
                $remainingAmount = $totalAmount;
                $currentPaymentDate = $rentalDate->copy();

                for ($p = 0; $p < $numPayments; $p++) {
                    if ($remainingAmount <= 0) break;
                    $paymentType = $p === 0 && $daysAgo > 0 ? 'deposit' : 'rental';
                    $amount = rand(10000, min($remainingAmount, 200000));
                    $paidAmount += $amount;
                    $remainingAmount -= $amount;
                    $paymentStatus = $remainingAmount <= 0 ? 'paid' : 'partial';

                    Payment::create([
                        'rental_id' => $rental->id,
                        'received_by' => $salesUser->id,
                        'payment_number' => 'PAY' . $currentPaymentDate->format('Ymd') . str_pad($totalPayments + 1, 4, '0', STR_PAD_LEFT),
                        'amount' => $amount,
                        'method' => ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])],
                        'type' => $paymentType,
                        'paid_at' => $currentPaymentDate,
                    ]);

                    $totalPayments++;
                    $currentPaymentDate->addDays(rand(0, 2));
                }

                $rental->update(['paid_amount' => $paidAmount, 'payment_status' => $paymentStatus]);

                if (rand(0, 1) === 1) {
                    $guarantee = Guarantee::create([
                        'rental_id' => $rental->id,
                        'type' => ['ktp', 'sim', 'deposit'][array_rand(['ktp', 'sim', 'deposit'])],
                        'deposit_amount' => rand(100000, 500000),
                        'description' => 'Deposit jaminan',
                        'status' => 'held',
                        'created_at' => $rentalDate,
                    ]);
                    $totalGuarantees++;
                }

                if (in_array($rentalStatus, ['returned'])) {
                    $actualReturnDate = $returnDueDate->copy()->addDays(rand(0, 2));
                    $lateDays = max(0, $actualReturnDate->diffInDays($returnDueDate, false));
                    $lateFee = $lateDays * ($totalAmount / $durationDays) * 0.5;

                    RentalReturn::create([
                        'rental_id' => $rental->id,
                        'returned_at' => $actualReturnDate->toDateString(),
                        'late_days' => $lateDays,
                        'late_fee' => $lateFee,
                        'condition' => ['baik', 'kurang_baik', 'rusak_ringan'][array_rand(['baik', 'kurang_baik', 'rusak_ringan'])],
                        'return_notes' => 'Pengembalian barang',
                    ]);

                    $totalReturns++;

                    if ($lateFee > 0) {
                        Payment::create([
                            'rental_id' => $rental->id,
                            'received_by' => $salesUser->id,
                            'payment_number' => 'PAY' . $actualReturnDate->format('Ymd') . str_pad($totalPayments + 1, 4, '0', STR_PAD_LEFT),
                            'amount' => $lateFee,
                            'method' => 'cash',
                            'type' => 'late_fee',
                            'paid_at' => $actualReturnDate,
                        ]);
                        $totalPayments++;
                    }
                }
            }
        }

        $this->command->info('Rentals Created: ' . $totalRentals);
        $this->command->info('Rental Items Created: ' . RentalItem::count());
        $this->command->info('Payments Created: ' . $totalPayments);
        $this->command->info('Guarantees Created: ' . $totalGuarantees);
        $this->command->info('Returns Created: ' . $totalReturns);
        $this->command->info('Activity Logs Created: ' . $totalActivities);
    }
}
