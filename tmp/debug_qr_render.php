<?php
$outputMode = 'pdf';
$rental = (object)[
    'invoice_number' => 'INV-TEST-001',
    'id' => 123,
    'rental_date' => '2025-01-15',
    'return_due_date' => '2025-01-20',
    'branch' => (object)['address' => 'Jakarta', 'phone' => '08123456789', 'email' => 'test@sewajas.id'],
    'payment_status' => 'paid',
    'customer' => (object)['name' => 'Test User', 'phone' => '08987654321', 'address' => 'Bandung'],
];

$paymentStatus = $rental->payment_status ?? 'unpaid';
$paymentLabel = match($paymentStatus) {
    'paid' => 'Lunas',
    'partial' => 'Sebagian',
    'overdue' => 'Terlambat',
    default => 'Belum Bayar',
};
$paymentVariant = match($paymentStatus) {
    'paid' => 'paid',
    'partial' => 'partial',
    'overdue' => 'overdue',
    default => 'unpaid',
};

$docLabel = 'INVOICE';
$docNumberValue = $rental->invoice_number;
$docDateValue = date('d M Y');
$rentalIdValue = $rental->id;
$companyAddress = $rental->branch->address ?? '-';
$companyPhone = $rental->branch->phone ?? '-';
$companyEmail = $rental->branch->email ?? '-';
$companyWebsite = 'www.sewajas.id';

$badgeText = $paymentLabel;
$badgeVariant = $paymentVariant;
$badgeClass = 'status-unpaid';
if (in_array($badgeVariant, ['paid', 'lunas'], true)) {
    $badgeClass = 'status-paid';
} elseif (in_array($badgeVariant, ['partial', 'sebagian'], true)) {
    $badgeClass = 'status-partial';
} elseif ($badgeVariant === 'overdue') {
    $badgeClass = 'status-overdue';
}

$signedRoute = 'https://example.com/invoice/' . $rental->id . '?signature=test123&expires=' . time();

echo "=== SIGNED ROUTE ===\n";
echo "Value: $signedRoute\n";
echo "Empty: " . (empty($signedRoute) ? 'YES' : 'NO') . "\n\n";

echo "=== QR GENERATION ===\n";
try {
    $qrPng = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->generate($signedRoute);
    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrPng);
    echo "Status: SUCCESS\n";
    echo "Base64 length: " . strlen($qrBase64) . "\n";
    echo "Base64 null? " . ($qrBase64 === null ? 'YES' : 'NO') . "\n";
    echo "Base64 empty? " . ($qrBase64 === '' ? 'YES' : 'NO') . "\n";
    echo "Starts with: " . substr($qrBase64, 0, 30) . "\n";
} catch (Throwable $e) {
    echo "Status: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    $qrBase64 = null;
}

echo "\n=== DOC-QR-CARD INPUT ===\n";
echo "qrBase64 null? " . ($qrBase64 === null ? 'YES' : 'NO') . "\n";
echo "qrBase64 empty? " . (empty($qrBase64) ? 'YES' : 'NO') . "\n";
echo "qrFallback: true\n";
echo "qrLabel: Scan untuk melihat invoice online\n";

echo "\n=== RENDER CHECK ===\n";
if (!empty($qrBase64)) {
    echo "Expected: <img src=\"data:image/png;base64,...\" class=\"qr-image\" width=\"80\" height=\"80\">\n";
} elseif (!empty($qrFallback)) {
    echo "Expected: fallback box (DIV with dashed border)\n";
} else {
    echo "Expected: NOTHING\n";
}
