@php $outputMode='pdf'; @endphp
@include('rentals.partials.premium-doc-head', ['docTitle' => 'Invoice ' . ($invoice->invoice_number ?? ''), 'outputMode' => $outputMode])
@include('rentals.partials.doc-parts-invoice', ['rental' => $invoice])
@include('rentals.partials.doc-close-html')

