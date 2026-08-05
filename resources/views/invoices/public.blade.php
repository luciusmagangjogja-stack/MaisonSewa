@php
    $outputMode = 'web';
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Invoice ' . ($rental->invoice_number ?? ''),
    'outputMode' => $outputMode,
])

{{-- Public invoice content --}}
@include('rentals.partials.doc-parts-invoice', ['rental' => $rental])
@include('rentals.partials.doc-close-html')
