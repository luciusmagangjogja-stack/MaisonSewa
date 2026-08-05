@php
    $backUrl = $backUrl ?? null;
    $pdfUrl = $pdfUrl ?? null;
    $printLabel = $printLabel ?? 'Cetak';
    $whatsAppUrl = $whatsAppUrl ?? null;
    $copyLinkAction = $copyLinkAction ?? null;
    $copyLinkLabel = $copyLinkLabel ?? 'Salin Tautan';
@endphp

<div class="no-print" style="margin-bottom: 18px; display:flex; flex-wrap:wrap; gap:10px;">
    @if(!empty($backUrl))
        <a class="doc-btn doc-btn-secondary" href="{{ $backUrl }}">Kembali</a>
    @endif

    @if(!empty($pdfUrl))
        <a class="doc-btn doc-btn-primary" href="{{ $pdfUrl }}">Download PDF</a>
    @endif

    @if(!empty($printLabel))
        <button class="doc-btn doc-btn-secondary" type="button" onclick="window.print()">{{ $printLabel }}</button>
    @endif

    @if(!empty($whatsAppUrl))
        <a class="doc-btn" href="{{ $whatsAppUrl }}" style="border-color: rgba(201,168,76,.35); background: rgba(201,168,76,.18); color: var(--brown-900);">WhatsApp</a>
    @endif

    @if(!empty($copyLinkAction))
        <button class="doc-btn doc-btn-secondary" type="button" onclick="{{ $copyLinkAction }}">{{ $copyLinkLabel }}</button>
    @endif
</div>
