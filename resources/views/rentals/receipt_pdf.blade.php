@php
    $outputMode = 'pdf';
    $receiptPrintedBy = auth()->user()?->name ?? ($rental->createdBy?->name ?? 'SewaJas System');
    $receiptPrintedAt = now()->format('d M Y');
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt ' . ($receipt['receipt_number'] ?? ''),
    'outputMode' => $outputMode,
])

@include('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt])
@include('rentals.partials.doc-premium-footer', ['printedBy' => $receiptPrintedBy, 'printedAt' => $receiptPrintedAt])
@include('rentals.partials.doc-close-html')


