@php
    $qrBase64 = $qrBase64 ?? null;
    $qrFallback = $qrFallback ?? false;
    $qrLabel = $qrLabel ?? null;
@endphp

<div class="qr-card">
    @if(!empty($qrBase64))
        <img src="{{ $qrBase64 }}" alt="QR Code" class="qr-image" width="95" height="95">
    @elseif($qrFallback)
        <div style="width:95px;height:95px;border:1px dashed #CBD5E1;display:inline-flex;align-items:center;justify-content:center;color:#94A3B8;font-size:9px;text-align:center;padding:4px;">
            QR unavailable
        </div>
    @endif
    @if(!empty($qrLabel))
        <div class="qr-caption">{{ $qrLabel }}</div>
    @endif
</div>
