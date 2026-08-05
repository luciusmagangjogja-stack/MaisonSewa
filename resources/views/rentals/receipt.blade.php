@php
    $outputMode = 'web';
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt',
    'outputMode' => $outputMode,
])

{{-- Web-only toolbar --}}
@include('rentals.partials.doc-action-buttons-web', [
    'backUrl' => route('rentals.show', $rental),
    'pdfUrl' => route('rentals.receipt.pdf', $rental),
    'printAction' => 'Print',
    'whatsAppUrl' => route('rentals.receipt.whatsapp', $rental),
    'copyLinkAction' => "navigator.clipboard.writeText('" . route('rentals.receipt.pdf', $rental) . "'); alert('Link PDF Receipt berhasil disalin!')",

    'copyLinkLabel' => 'Copy Link Receipt',
])

@include('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt])

@php
    $receiptPrintedBy = auth()->user()?->name ?? ($rental->createdBy?->name ?? 'SewaJas System');
    $receiptPrintedAt = now()->format('d M Y');
@endphp
@include('rentals.partials.doc-premium-footer', ['printedBy' => $receiptPrintedBy, 'printedAt' => $receiptPrintedAt])

@include('rentals.partials.doc-close-html')

