# Enterprise Consistency Audit Plan

## Root Cause
All inventory-mutating methods (except `update()`) read `stock_available` without `lockForUpdate()`, allowing race conditions under concurrent access.

## Changes Needed

### File 1: `app/Services/RentalService.php`

#### A. `createRental()` — around line 115-117
Replace:
```php
$product = Product::findOrFail($item['product_id']);
// ... later
$newStock = $product->stock_available - $item['quantity'];
$product->update(['stock_available' => $newStock, ...]);
```
With:
```php
$product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
```

#### B. `processReturn()` — around line 219 (the stock restore loop)
Replace:
```php
$product = $item->product->fresh();
$newStock = $product->stock_available + $item->quantity;
$product->update([...]);
```
With:
```php
$product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
$newStock = $product->stock_available + $item->quantity;
$product->update([...]);
```

### File 2: `app/Http/Controllers/RentalController.php`

#### C. `destroy()` — around line 286
Replace:
```php
$product = $item->product->fresh();
```
With:
```php
$product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
```

#### D. `cancel()` — around line 325
Replace:
```php
$product = $item->product->fresh();
```
With:
```php
$product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
```

#### E. `updateStatus()` — both stock loops (for 'returned' and 'active' paths, around lines 481 and 495)
Replace:
```php
$product = $item->product->fresh();
```
With:
```php
$product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
```

## Verification
After applying: run `php -l` on both files. No syntax errors expected.
</｜DSML｜parameter>
</｜DSML｜parameter>
</create_file>

Now let me apply the fixes:

<edit_file>
<path>c:/xampp82/htdocs/rental-jas-main/app/Services/RentalService.php</path>
<old_str>    public function createRental(array $data): Rental
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $durationDays = (int) $data['duration_days'];

            // ─── STEP 1: Collect branch IDs from all selected products ───
            $branchIds = collect($data['items'])
                ->pluck('product_id')
                ->map(fn($id) => Product::findOrFail($id)->branch_id)
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

            // Calculate subtotal
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal += $product->rental_price * $item['quantity'] * $durationDays;
            }

            $discount = $data['discount'] ?? 0;
            $totalAmount = $subtotal - $discount;

            // Create rental with resolved branchId
            $rental = Rental::create([
                'invoice_number' => $this->generateInvoiceNumber($branchId),
                'branch_id'      => $branchId,
                'customer_id'    => $data['customer_id'],
                'created_by'     => $user->id,
                'rental_date'    => $data['rental_date'],
                'return_due_date' => Carbon::parse($data['rental_date'])->addDays($durationDays),
                'duration_days'  => $durationDays,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total_amount'   => $totalAmount,
                'paid_amount'    => 0,
                'payment_status' => Rental::PAYMENT_UNPAID,
                'payment_method' => $data['payment_method'] ?? null,
                'rental_status'  => Rental::STATUS_WAITING,
                'notes'          => $data['notes'] ?? null,
            ]);

            // Create rental items & reduce stock
            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $product->rental_price * $item['quantity'] * $durationDays;

                RentalItem::create([
                    'rental_id'    => $rental->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_size' => $item['product_size'],
                    'product_color' => $product->color,
                    'quantity'     => $item['quantity'],
                    'price_per_day' => $product->rental_price,
                    'duration_days' => $durationDays,
                    'subtotal'     => $itemSubtotal,
                ]);

                // Reduce stock
                $newStock = $product->stock_available - $item['quantity'];
                $product->update([
                    'stock_available' => $newStock,
                    'status' => $newStock <= 0 ? 'rented' : $product->status,
                ]);
            }</｜DSML｜parameter>
</old_str>
<new_str>    public function createRental(array $data): Rental
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $durationDays = (int) $data['duration_days'];

            // ─── STEP 1: Collect branch IDs from all selected products ───
            $branchIds = collect($data['items'])
                ->pluck('product_id')
                ->map(fn($id) => Product::whereKey($id)->lockForUpdate()->value('branch_id'))
                ->unique();

            // ─── STEP 2: Reject mixed-branch rentals ───
            if ($branchIds->count() > 1) {
                throw ValidationException::withMessages([
                    'items'
