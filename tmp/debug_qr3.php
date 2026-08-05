<?php
require __DIR__ . '/../vendor/autoload.php';

use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;

$route = 'https://example.com/invoice/123?signature=test123';

try {
    $renderer = new Png();
    $renderer->setWidth(200);
    $renderer->setHeight(200);
    $writer = new Writer($renderer);
    $qrPng = $writer->writeString($route);
    echo "SUCCESS\n";
    echo "Route: $route\n";
    echo "Binary length: " . strlen($qrPng) . "\n";
    $base64 = 'data:image/png;base64,' . base64_encode($qrPng);
    echo "Base64 length: " . strlen($base64) . "\n";
    echo "Base64 starts with: " . substr($base64, 0, 50) . "\n";
    echo "Base64 ends with: " . substr($base64, -50) . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
