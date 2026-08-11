@php
    $rental = $rental ?? null;
    $receipt = $receipt ?? [];
@endphp

@php
    $paymentStatus = $rental->payment_status ?? 'unpaid';
    $paymentLabel = $paymentStatus === 'paid' ? 'Lunas' : ($paymentStatus === 'partial' ? 'Sebagian' : 'Belum Bayar');
    $paymentVariant = $paymentStatus === 'paid' ? 'paid' : ($paymentStatus === 'partial' ? 'partial' : 'pending');

    $deposit = $receipt['deposit'] ?? $rental->guarantees->where('type', 'deposit')->sum('deposit_amount');
    $lateFee = $receipt['late_fee'] ?? ($rental->late_fee ?? 0);
    $damageFee = $receipt['damage_fee'] ?? ($rental->items->sum('damage_fee') ?? 0);
@endphp

@include('rentals.partials.doc-brand-header', [
    'docLabel' => 'RECEIPT',
    'docNumberLabel' => 'Nomor Receipt',
    'docNumberValue' => $receipt['receipt_number'] ?? ('RCP-' . ($rental->id ?? '')),
    'docDateValue' => now('Asia/Jakarta')->format('d M Y H:i'),
    'qrRoute' => route('rentals.receipt.show', $rental),
    'companyAddress' => $rental->branch->address ?? '-',
    'companyPhone' => $rental->branch->phone ?? '-',
    'companyEmail' => 'support@sewajas.id',
    'companyWebsite' => 'www.sewajas.id',
    'status' => $paymentStatus,
    'badgeText' => $paymentLabel,
    'variant' => $paymentVariant,
])

<div style="margin-top: 10px;">
    <div class="section-title" style="padding-left:8px;">Dokumen</div>

    <div class="info-grid">
        @include('rentals.partials.doc-customer-info', [
            'customer'=>$rental->customer,
            'createdBy'=>$rental->createdBy,
            'branch'=>$rental->branch,
        ])
    </div>
</div>

<div style="margin-top: 8px;">
    <div class="section-title" style="padding-left:8px; margin-bottom:6px;">Rental Items</div>
    @include('rentals.partials.doc-item-list', ['items'=>$rental->items])
</div>

@include('rentals.partials.doc-price-summary', [
    'subtotal' => $rental->subtotal ?? 0,
    'discount' => $rental->discount ?? 0,
    'deposit' => $deposit ?? 0,
    'lateFee' => $lateFee ?? 0,
    'damageFee' => $damageFee ?? 0,
    'totalAmount' => $rental->total_amount ?? 0,
    'paidAmount' => $rental->paid_amount ?? 0,
    'changeAmount' => $rental->change_amount ?? 0,
    'remainingBalance' => max(0, ($rental->total_amount ?? 0) - ($rental->paid_amount ?? 0)),
    'guaranteeType' => optional($rental->guarantees->first())->type,
])
@if($rental->fine_status === 'unpaid' || $rental->fine_status === 'partial')
    <div style="margin-top:6px; padding:6px 10px; background:#FEF3C7; border-left:3px solid #F59E0B; border-radius:6px; font-size:10px;">
        <span style="font-weight:700; color:#92400E;">STATUS DENDA: {{ match($rental->fine_status) { 'unpaid' => 'BELUM DIBAYAR', 'partial' => 'SEBAGIAN', default => strtoupper($rental->fine_status) } }}</span>
        @if($rental->fine_amount > 0)
            <span style="color:#92400E; margin-left:6px;">Rp {{ number_format($rental->fine_amount, 0, ',', '.') }}</span>
        @endif
    </div>
@endif

@include('rentals.partials.doc-notes', [
    'notes' => 'Receipt ini dibuat otomatis oleh sistem dan tidak memerlukan tanda tangan.',
])

@include('rentals.partials.doc-payments-history', [
    'payments' => $rental->payments,
])
