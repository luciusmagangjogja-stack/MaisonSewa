<?php
echo "=== TESTING ImagickImageBackEnd WITHOUT Imagick ===\n";
try {
    $backend = new \BaconQrCode\Renderer\Image\ImagickImageBackEnd('png');
    echo "Result: SUCCESS (Imagick is available)\n";
} catch (Throwable $e) {
    echo "Result: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== TESTING QrCode::format('png') ===\n";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate('https://example.com');
    echo "Result: SUCCESS\n";
    echo "Length: " . strlen($qr) . "\n";
} catch (Throwable $e) {
    echo "Result: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
