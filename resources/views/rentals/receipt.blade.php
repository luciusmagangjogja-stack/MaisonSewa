@php
    $outputMode = 'web';
@endphp

@include('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt',
    'outputMode' => $outputMode,
])

<div class="no-print doc-toolbar-wrapper">
    <div class="no-print doc-toolbar">
        <a class="doc-btn doc-btn-ghost" href="{{ route('rentals.show', $rental) }}">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> <span class="btn-text">Kembali</span>
        </a>
        <a class="doc-btn doc-btn-primary" href="{{ route('rentals.receipt.pdf', $rental) }}">
            <i data-lucide="download" class="w-4 h-4"></i> <span class="btn-text">Download PDF</span>
        </a>
        <button class="doc-btn doc-btn-secondary" type="button" onclick="window.print()">
            <i data-lucide="printer" class="w-4 h-4"></i> <span class="btn-text">Print</span>
        </button>
        <a class="doc-btn doc-btn-whatsapp" href="javascript:void(0)" 
           title="PDF akan otomatis terdownload — lampirkan manual di chat WhatsApp yang terbuka"
           onclick="downloadPdfAndOpenWa('{{ route('rentals.receipt.pdf', $rental) }}', '{{ addslashes($rental->customer->name ?? 'Customer') }}', '{{ $rental->invoice_number }}', '{{ preg_replace('/[^0-9]/', '', str_starts_with($rental->customer->phone ?? '', '0') ? '62'.substr($rental->customer->phone, 1) : ($rental->customer->phone ?? '')) }}')">
            <i data-lucide="message-circle" class="w-4 h-4"></i> <span class="btn-text">WhatsApp</span>
        </a>
        <button class="doc-btn doc-btn-secondary" type="button" onclick="navigator.clipboard.writeText('{{ route('rentals.receipt.pdf', $rental) }}'); alert('Link PDF Receipt berhasil disalin!')">
            <i data-lucide="link" class="w-4 h-4"></i> <span class="btn-text">Copy Link Receipt</span>
        </button>
    </div>
</div>

@include('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt])

<script>
function downloadPdfAndOpenWa(pdfUrl, customerName, invoiceNumber, customerPhone) {
    if (!customerPhone) {
        alert('Nomor WhatsApp customer tidak tersedia.');
        return;
    }

    var link = document.createElement('a');
    link.href = pdfUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    var message = encodeURIComponent(
        "Halo " + customerName + ",\\n\\n" +
        "Berikut receipt sewa Anda " + invoiceNumber + ". " +
        "Mohon cek file PDF yang baru saja di-download, lalu lampirkan di sini ya.\\n\\n" +
        "Terima kasih."
    );

    setTimeout(function() {
        window.open("https://wa.me/" + customerPhone + "?text=" + message, '_blank');
    }, 500);
}
</script>

@include('rentals.partials.doc-close-html')

