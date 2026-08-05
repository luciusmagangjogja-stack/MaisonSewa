<?php
require __DIR__ . '/../vendor/autoload.php';

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;

$route = 'https://example.com/invoice/123?signature=test123&expires=' . time();

echo "=== ENVIRONMENT CHECK ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Imagick extension: " . (extension_loaded('imagick') ? 'YES' : 'NO') . "\n";
echo "GD extension: " . (extension_loaded('gd') ? 'YES' : 'NO') . "\n";

try {
    $renderer = new ImageRenderer(
        new RendererStyle(200),
        new ImagickImageBackEnd()
    );
    $writer = new Writer($renderer);
    $qrBinary = $writer->writeString($route);
    
    $base64 = 'data:image/png;base64,' . base64_encode($qrBinary);
    echo "\n=== QR GENERATION RESULT ===\n";
    echo "Status: SUCCESS\n";
    echo "Route: $route\n";
    echo "Binary length: " . strlen($qrBinary) . "\n";
    echo "Base64 length: " . strlen($base64) . "\n";
    echo "Base64 null? " . ($base64 === null ? 'YES' : 'NO') . "\n";
    echo "Base64 empty? " . ($base64 === '' ? 'YES' : 'NO') . "\n";
    echo "Starts with: " . substr($base64, 0, 50) . "\n";
} catch (Throwable $e) {
    echo "\n=== QR GENERATION RESULT ===\n";
    echo "Status: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    $base64 = null;
}

echo "\n=== DOC-QR-CARD SIMULATION ===\n";
echo "qrBase64: " . ($base64 ? 'SET (length=' . strlen($base64) . ')' : 'NULL/EMPTY') . "\n";
echo "qrFallback: true\n";
echo "qrLabel: Scan untuk melihat invoice online\n";

echo "\n=== EXPECTED RENDER ===\n";
if (!empty($base64)) {
    echo "QR IMG TAG: <img src=\"data:image/png;base64,...\" class=\"qr-image\" width=\"80\" height=\"80\">\n";
    echo "QR CAPTION: Scan untuk melihat invoice online\n";
} else {
    echo "FALLBACK BOX: DIV with dashed border, 80x80px, text 'QR unavailable'\n";
}
