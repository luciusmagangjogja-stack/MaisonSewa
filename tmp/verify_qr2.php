<?php
require __DIR__ . '/../vendor/autoload.php';

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

echo "=== QR GENERATION DEBUG ===\n\n";

$route = 'https://example.com/invoice/123?expires=' . time() . '&signature=abc123';

try {
    $renderer = new ImageRenderer(
        new RendererStyle(200),
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    $qrSvg = $writer->writeString($route);
    
    echo "STEP 1: gettype((string)\$qrSvg)\n";
    $type = gettype((string) $qrSvg);
    echo "Result: $type\n";
    echo "Expected: string\n";
    echo "Match: " . ($type === 'string' ? 'YES' : 'NO') . "\n\n";
    
    echo "STEP 2: strlen((string)\$qrSvg)\n";
    $length = strlen((string) $qrSvg);
    echo "Result: $length\n";
    echo "Expected: > 0\n";
    echo "Match: " . ($length > 0 ? 'YES' : 'NO') . "\n\n";
    
    echo "STEP 3: substr((string)\$qrSvg, 0, 150)\n";
    $prefix = substr((string) $qrSvg, 0, 150);
    echo "Result: $prefix\n";
    echo "Expected starts with: <svg or <?xml\n";
    $startsCorrect = (str_starts_with($prefix, '<svg') || str_starts_with($prefix, '<?xml'));
    echo "Match: " . ($startsCorrect ? 'YES' : 'NO') . "\n\n";
    
    echo "STEP 4: base64 encoding\n";
    $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode((string) $qrSvg);
    $base64Length = strlen($qrBase64);
    echo "Result: strlen(\$qrBase64) = $base64Length\n";
    echo "Expected: > 0\n";
    echo "Match: " . ($base64Length > 0 ? 'YES' : 'NO') . "\n\n";
    
    echo "STEP 5: Verify mime type\n";
    $startsWith = str_starts_with($qrBase64, 'data:image/svg+xml;base64,');
    echo "Result: " . ($startsWith ? 'YES - correct mime type' : 'NO - wrong mime type') . "\n\n";
    
    echo "STEP 6: Verify HTML output\n";
    $html = '<img src="' . $qrBase64 . '" alt="QR Code" class="qr-image" width="80" height="80">';
    echo "HTML: $html\n\n";
    
    echo "STEP 7: Verify SVG validity\n";
    $svgString = (string) $qrSvg;
    $xmlDom = new DOMDocument();
    $xmlLoaded = @$xmlDom->loadXML($svgString);
    if ($xmlLoaded) {
        echo "Result: VALID XML\n";
    } else {
        echo "Result: INVALID XML\n";
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            echo "Parser error: " . $error->message . "\n";
        }
        libxml_clear_errors();
    }
    
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Class: " . get_class($e) . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
