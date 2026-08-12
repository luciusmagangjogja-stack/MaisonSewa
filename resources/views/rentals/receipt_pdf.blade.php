@php
    $outputMode = 'pdf';
    $receiptPrintedBy = auth()->user()?->name ?? ($rental->createdBy?->name ?? \App\Services\SettingsService::get('company_name', 'SewaJas'));
    $receiptPrintedAt = now()->format('d M Y');
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt ' . ($receipt['receipt_number'] ?? ''),
    'outputMode' => $outputMode,
])

@include('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt])
@include('rentals.partials.doc-close-html')


