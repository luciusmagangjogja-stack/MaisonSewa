<?php
require __DIR__ . '/../vendor/autoload.php';

// Simulate the exact logic from doc-brand-header.blade.php
$rental = (object)[
    'id' => 123,
    'invoice_number' => 'INV-TEST-001',
    'rental_date' => '2025-01-15',
    'branch' => (object)['address' => 'Jakarta', 'phone' => '08123456789', 'email' => 'test@sewajas.id'],
];

$docLabel = 'INVOICE';
$docNumberValue = $rental->invoice_number;
$docDateValue = date('d M Y');
$rentalIdValue = $rental->id;
$companyAddress = $rental->branch->address ?? '-';
$companyPhone = $rental->branch->phone ?? '-';
$companyEmail = $rental->branch->email ?? '-';
$companyWebsite = 'www.sewajas.id';

$badgeText = 'Lunas';
$badgeVariant = 'paid';
$badgeClass = 'status-paid';

$qrRoute = 'https://example.com/invoice/' . $rental->id . '?expires=' . time() . '&signature=abc123';

echo "=== VARIABLES ===\n";
echo "qrRoute: $qrRoute\n";
echo "qrRoute empty? " . (empty($qrRoute) ? 'YES' : 'NO') . "\n\n";

echo "=== QR GENERATION ATTEMPT ===\n";
$qrBase64 = null;
if (!empty($qrRoute)) {
    try {
        $qrPng = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($qrRoute);
        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrPng);
        echo "Status: SUCCESS\n";
        echo "qrBase64 null? " . ($qrBase64 === null ? 'YES' : 'NO') . "\n";
        echo "qrBase64 empty? " . ($qrBase64 === '' ? 'YES' : 'NO') . "\n";
        echo "strlen(qrBase64): " . strlen($qrBase64) . "\n";
    } catch (Throwable $e) {
        echo "Status: FAILED\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo "qrBase64 set to: null\n";
        $qrBase64 = null;
    }
}

echo "\n=== DOC-QR-CARD INPUT ===\n";
echo "qrBase64: " . ($qrBase64 ? 'SET' : 'NULL') . "\n";
echo "qrFallback: true\n";
echo "qrLabel: Scan untuk melihat invoice online\n";

echo "\n=== EXPECTED RENDER OUTPUT ===\n";
if (!empty($qrBase64)) {
    echo "<div class=\"qr-card\">\n";
    echo "  <img src=\"data:image/png;base64,...\" alt=\"QR Code\" class=\"qr-image\" width=\"80\" height=\"80\">\n";
    echo "  <div class=\"qr-caption\">Scan untuk melihat invoice online</div>\n";
    echo "</div>\n";
} else {
    echo "<div class=\"qr-card\">\n";
    echo "  <div style=\"width:80px;height:80px;border:1px dashed #CBD5E1;...\">QR unavailable</div>\n";
    echo "</div>\n";
}
