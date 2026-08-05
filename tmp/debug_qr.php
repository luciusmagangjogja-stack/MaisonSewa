<?php
require __DIR__ . '/../vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Facades\QrCode;

$route = 'https://example.com/test';
try {
    $qrPng = QrCode::format('png')->size(200)->generate($route);
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
