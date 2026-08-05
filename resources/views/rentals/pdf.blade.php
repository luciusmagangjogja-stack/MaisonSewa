@php
    $outputMode = 'pdf';
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Invoice ' . ($rental->invoice_number ?? ''),
    'outputMode' => $outputMode,
])

{{-- Premium Invoice content for PDF export (no interactive toolbar) --}}
@include('rentals.partials.doc-parts-invoice', ['rental'=>$rental])
@include('rentals.partials.doc-close-html')

