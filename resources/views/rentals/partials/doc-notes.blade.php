@php
    $notes = $notes ?? null;
@endphp

@if(!empty($notes))
    <div class="notes-box" style="background:#F8FAFC; border-left:3px solid #1E40AF; padding:10px 12px; margin-top:10px; font-size:10px; color:#64748B; line-height:1.5;">
        <div style="font-weight:700; color:#1E40AF; margin-bottom:2px; font-size:10px;">Catatan</div>
        <div style="color:#374151; line-height:1.4;">{{ $notes }}</div>
    </div>
@endif
