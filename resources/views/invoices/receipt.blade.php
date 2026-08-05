@php $outputMode='web'; @endphp
@include('rentals.partials.premium-doc-head', ['docTitle'=>'Invoice Receipt','outputMode'=>$outputMode])
@include('rentals.partials.doc-parts-receipt', ['rental'=>$invoice, 'receipt'=>$payload])

@php
    $receiptPrintedBy = auth()->user()?->name ?? ($invoice->createdBy?->name ?? 'SewaJas System');
    $receiptPrintedAt = now()->format('d M Y');
@endphp
@include('rentals.partials.doc-premium-footer', ['printedBy' => $receiptPrintedBy, 'printedAt' => $receiptPrintedAt])
@include('rentals.partials.doc-close-html')

