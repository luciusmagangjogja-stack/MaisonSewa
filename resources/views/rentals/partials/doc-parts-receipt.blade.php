@php
    $rental = $rental ?? null;
    $receipt = $receipt ?? [];
@endphp

@php
    $paymentStatus = $rental->payment_status ?? 'unpaid';
    $paymentLabel = $paymentStatus === 'paid' ? 'Lunas' : ($paymentStatus === 'partial' ? 'Sebagian' : 'Belum Bayar');
    $paymentVariant = $paymentStatus === 'paid' ? 'paid' : ($paymentStatus === 'partial' ? 'partial' : 'pending');

    $deposit = $receipt['deposit'] ?? ($rental->items->sum(fn($i) => $i->product?->deposit_price ?? 0));
    $lateFee = $receipt['denda'] ?? ($rental->late_fee ?? 0);
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

        @include('rentals.partials.doc-rental-info-summary', ['rental'=>$rental])
    </div>
</div>

<div style="margin-top: 14px;">
    <div class="section-title" style="margin-bottom: 10px; padding-left:8px;">Rental Items</div>
    @include('rentals.partials.doc-item-list', ['items'=>$rental->items])
</div>

@include('rentals.partials.doc-price-summary', [
    'subtotal' => $rental->subtotal ?? 0,
    'discount' => $rental->discount ?? 0,
    'deposit' => $deposit ?? 0,
    'lateFee' => $lateFee ?? 0,
    'damageFee' => 0,
    'totalAmount' => $rental->total_amount ?? 0,
])

@include('rentals.partials.doc-notes', [
    'notes' => 'Receipt ini dibuat otomatis oleh sistem dan tidak memerlukan tanda tangan.',
])

@include('rentals.partials.doc-payments-history', [
    'payments' => $rental->payments,
])
