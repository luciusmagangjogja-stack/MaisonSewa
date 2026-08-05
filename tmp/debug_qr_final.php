<?php
require __DIR__ . '/../vendor/autoload.php';

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;

echo "=== CLASS CHECK ===\n";
echo "ImagickImageBackEnd: " . (class_exists('BaconQrCode\Renderer\Image\ImagickImageBackEnd') ? 'EXISTS' : 'NOT FOUND') . "\n";
echo "Imagick extension: " . (extension_loaded('imagick') ? 'LOADED' : 'NOT LOADED') . "\n";

echo "\n=== INSTANTIATION TEST ===\n";
try {
    $backend = new ImagickImageBackEnd('png');
    echo "Backend instantiated: SUCCESS\n";
} catch (Throwable $e) {
    echo "Backend instantiated: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
}

echo "\n=== FULL QR GENERATION ===\n";
$route = 'https://example.com/invoice/123?expires=' . time() . '&signature=abc123';
try {
    $renderer = new ImageRenderer(
        new RendererStyle(200),
        new ImagickImageBackEnd('png')
    );
    $writer = new Writer($renderer);
    $qrBinary = $writer->writeString($route);
    echo "Status: SUCCESS\n";
    echo "Binary length: " . strlen($qrBinary) . "\n";
    
    $base64 = 'data:image/png;base64,' . base64_encode($qrBinary);
    echo "Base64 length: " . strlen($base64) . "\n";
    echo "Base64 null? " . ($base64 === null ? 'YES' : 'NO') . "\n";
    echo "First 80 chars: " . substr($base64, 0, 80) . "\n";
} catch (Throwable $e) {
    echo "Status: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    $base64 = null;
}

echo "\n=== BLADE SIMULATION ===\n";
echo "qrBase64: " . ($base64 ? 'SET (length=' . strlen($base64) . ')' : 'NULL/EMPTY') . "\n";
if ($base64) {
    echo "Expected IMG tag: <img src=\"data:image/png;base64,...\" class=\"qr-image\" width=\"80\" height=\"80\">\n";
} else {
    echo "Expected: FALLBACK BOX\n";
}
