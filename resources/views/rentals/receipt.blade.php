@php
    $outputMode = 'web';
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt',
    'outputMode' => $outputMode,
])

<div class="no-print" style="margin-bottom: 18px; display:flex; flex-wrap:wrap; gap:10px;">
    <a class="doc-btn doc-btn-secondary" href="{{ route('rentals.show', $rental) }}">Kembali</a>
    <a class="doc-btn doc-btn-primary" href="{{ route('rentals.receipt.pdf', $rental) }}">Download PDF</a>
    <button class="doc-btn doc-btn-secondary" type="button" onclick="window.print()">Print</button>
    <a class="doc-btn doc-btn-whatsapp" href="{{ route('rentals.receipt.whatsapp', $rental) }}">WhatsApp</a>
    <button class="doc-btn doc-btn-secondary" type="button" onclick="navigator.clipboard.writeText('{{ route('rentals.receipt.pdf', $rental) }}'); alert('Link PDF Receipt berhasil disalin!')">Copy Link Receipt</button>
</div>

@include('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt])

@include('rentals.partials.doc-close-html')

