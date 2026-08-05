@php
    $rental = $rental ?? null;
    $createdBy = $createdBy ?? ($rental->createdBy ?? null);
    $printedBy = $printedBy ?? auth()->user();
    $printedByName = $printedBy?->name ?? $createdBy?->name ?? 'SewaJas System';
    $printedAt = now()->format('d M Y');
@endphp

<div style="font-size:10px; color:#64748B; margin-top:4px;">
    Dicetak oleh: {{ $printedByName }} · {{ $printedAt }}
</div>
