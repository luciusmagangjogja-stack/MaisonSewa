<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
$user = new class {
    public function name() { return 'Test'; }
    public $branch_id = 1;
    public $id = 1;
    public $role = 'super_admin';
    public function isSuperAdmin() { return true; }
};
Auth::shouldReceive('user')->andReturn($user);

$r = \App\Models\Rental::with(['items.product'])->find(185);
echo 'Rental #185 status: ' . $r->rental_status . PHP_EOL;
foreach($r->items as $i) {
    echo '  Product: ' . $i->product_name . ' (ID: ' . $i->product_id . ') qty: ' . $i->quantity . ' stock_available: ' . $i->product->stock_available . PHP_EOL;
}

echo PHP_EOL . '=== Product stock before/after ===' . PHP_EOL;
$product = \App\Models\Product::find(4); // Jas Wisuda Hitam Modern
echo 'Product: ' . $product->name . PHP_EOL;
echo 'stock_available: ' . $product->stock_available . PHP_EOL;
echo 'stock_total: ' . $product->stock_total . PHP_EOL;
