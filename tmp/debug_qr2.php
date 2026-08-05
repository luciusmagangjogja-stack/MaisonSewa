<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $route = 'https://example.com/invoice/123?signature=test123&expires=' . time();
    $qrPng = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($route);
    $base64 = 'data:image/png;base64,' . base64_encode($qrPng);
    echo "SUCCESS\n";
    echo "Route: $route\n";
    echo "Base64 length: " . strlen($base64) . "\n";
    echo "Base64 starts with: " . substr($base64, 0, 50) . "\n";
    echo "Base64 ends with: " . substr($base64, -50) . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
