@php
    /**
     * Premium Invoice Web
     * Variables: $rental
     */
    $outputMode = 'web';
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Invoice ' . ($rental->invoice_number ?? ''),
    'outputMode' => $outputMode,
])

{{-- Web-only toolbar --}}
@include('rentals.partials.doc-action-buttons-web', [
    'backUrl' => route('rentals.show', $rental),
    'pdfUrl' => route('rentals.pdf', $rental),
    'printAction' => 'Print',
    'whatsAppUrl' => route('rentals.whatsapp', $rental),
    'copyLinkAction' => "navigator.clipboard.writeText('" . route('rentals.pdf', $rental) . "'); alert('Link PDF Invoice berhasil disalin!')",

    'copyLinkLabel' => 'Copy Link Invoice',
])

{{-- Premium Invoice Content --}}
@include('rentals.partials.doc-parts-invoice', ['rental'=>$rental])

@include('rentals.partials.doc-close-html')

