<?php
/**
 * Runtime verification of the (string) cast fix for QR generation.
 * This simulates the exact flow in doc-brand-header.blade.php after the fix.
 */
require __DIR__ . '/../vendor/autoload.php';

use SimpleSoftwareIO\QrCode\Facades\QrCode;

$qrRoute = 'https://example.com/invoice/123?expires=' . time() . '&signature=abc123';

echo "=== RUNTIME VERIFICATION ===\n\n";

echo "STEP 1: Simulate doc-brand-header.blade.php QR generation\n";
echo "qRRoute: $qrRoute\n\n";

try {
    // This is the EXACT line from the fixed doc-brand-header.blade.php
    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($qrRoute);
    $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode((string) $qrSvg);
    
    echo "STEP 2: Verify qrBase64 is NOT NULL\n";
    echo "qrBase64 null?: " . ($qrBase64 === null ? 'YES - FAIL' : 'NO - PASS') . "\n\n";
    
    echo "STEP 3: Verify gettype((string)\$qrSvg)\n";
    $type = gettype((string) $qrSvg);
    echo "Result: $type\n";
    echo "Expected: string\n";
    echo "Status: " . ($type === 'string' ? 'PASS' : 'FAIL') . "\n\n";
    
    echo "STEP 4: Verify strlen((string)\$qrSvg)\n";
    $length = strlen((string) $qrSvg);
    echo "Result: $length\n";
    echo "Expected: > 0\n";
    echo "Status: " . ($length > 0 ? 'PASS' : 'FAIL') . "\n\n";
    
    echo "STEP 5: Verify SVG prefix\n";
    $prefix = substr((string) $qrSvg, 0, 150);
    echo "First 150 chars: $prefix\n";
    $startsCorrect = (str_starts_with($prefix, '<svg') || str_starts_with($prefix, '<?xml'));
    echo "Expected: starts with <svg or <?xml\n";
    echo "Status: " . ($startsCorrect ? 'PASS' : 'FAIL') . "\n\n";
    
    echo "STEP 6: Verify base64 string\n";
    echo "Length: " . strlen($qrBase64) . "\n";
    echo "Starts with: " . substr($qrBase64, 0, 35) . "\n";
    echo "Expected: data:image/svg+xml;base64,\n";
    echo "Status: " . (str_starts_with($qrBase64, 'data:image/svg+xml;base64,') ? 'PASS' : 'FAIL') . "\n\n";
    
    echo "STEP 7: Verify HTML output (simulated doc-qr-card)\n";
    $html = '<img src="' . $qrBase64 . '" alt="QR Code" class="qr-image" width="80" height="80">';
    echo "HTML: $html\n\n";
    
    echo "STEP 8: Verify SVG validity\n";
    $svgString = (string) $qrSvg;
    $xmlDom = new DOMDocument();
    $xmlLoaded = @$xmlDom->loadXML($svgString);
    if ($xmlLoaded) {
        echo "Status: VALID XML - PASS\n";
    } else {
        echo "Status: INVALID XML - FAIL\n";
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            echo "Error: " . $error->message . "\n";
        }
    }
    
    echo "\n=== FINAL RESULT ===\n";
    if ($type === 'string' && $length > 0 && $startsCorrect && str_starts_with($qrBase64, 'data:image/svg+xml;base64,') && $xmlLoaded) {
        echo "PASS - QR code generation works correctly\n";
        exit(0);
    } else {
        echo "FAIL - QR code generation failed\n";
        exit(1);
    }
    
} catch (Throwable $e) {
    echo "FAIL - Exception caught:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
