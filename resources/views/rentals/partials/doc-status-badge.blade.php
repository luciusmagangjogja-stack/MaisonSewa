@php
    $status = $status ?? null;
    $badgeText = $badgeText ?? $status;
    $variant = $variant ?? 'pending'; // pending|paid|partial|cancelled|refunded|brown
@endphp

@php
    $styles = [
        'pending' => 'background: var(--gray-100); color: var(--gray-900); border: 1px solid var(--border);',
        'paid' => 'background: rgba(16,185,129,.12); color: rgba(6,95,70,1); border: 1px solid rgba(16,185,129,.25);',
        'partial' => 'background: var(--primary-100); color: var(--primary-600); border: 1px solid rgba(59,130,246,.22);',
        'cancelled' => 'background: rgba(239,68,68,.10); color: rgba(153,27,27,1); border: 1px solid rgba(239,68,68,.22);',
        'refunded' => 'background: rgba(59,130,246,.10); color: rgba(30,64,175,1); border: 1px solid rgba(59,130,246,.22);',
    ];
    $style = $styles[$variant] ?? $styles['pending'];
@endphp

<span style="display:inline-flex; align-items:center; gap:10px; padding:8px 14px; border-radius:999px; font-size:12px; font-weight:600; {{ $style }} box-shadow: 0 2px 8px rgba(17,24,39,.04);">
    {{ $badgeText }}
</span>
