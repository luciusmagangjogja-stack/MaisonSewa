@php
    $customerTitle = $customerTitle ?? 'Informasi Customer';
    $rentalTitle = $rentalTitle ?? 'Detail Rental';
@endphp

<div class="info-grid">
    <div class="info-card">
        <div class="section-title">{{ $customerTitle }}</div>
        <div class="info-row"><span class="k">Nama</span><span class="v">{{ optional($customer)->name ?? '-' }}</span></div>
        <div class="info-row"><span class="k">Nomor HP</span><span class="v">{{ optional($customer)->phone ?? '-' }}</span></div>
        <div class="info-row"><span class="k">Alamat</span><span class="v">{{ $customer->address ?? '-' }}</span></div>
        <div class="info-row"><span class="k">Sales</span><span class="v">{{ optional($createdBy)->name ?? '-' }}</span></div>
        <div class="info-row"><span class="k">Cabang</span><span class="v">{{ optional($branch)->name ?? '-' }}</span></div>
    </div>

    <div class="info-card">
        <div class="section-title">{{ $rentalTitle }}</div>
        <div class="info-row"><span class="k">Tanggal Rental</span><span class="v">{{ optional($rental->rental_date)->format('d M Y') ?? '-' }}</span></div>
        <div class="info-row"><span class="k">Tanggal Kembali</span><span class="v">{{ optional($rental->return_due_date)->format('d M Y') ?? '-' }}</span></div>
        <div class="info-row"><span class="k">Durasi</span><span class="v">{{ $rental->duration_days ?? 0 }} hari</span></div>
        <div class="info-row" style="align-items:center;">
            <span class="k">Status Rental</span>
            <span class="v" style="text-align:right; font-weight:800; color: var(--text);">{{ ucfirst($rental->rental_status ?? '-') }}</span>
        </div>
    </div>
</div>
