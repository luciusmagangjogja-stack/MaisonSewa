@php
    $rental = $rental ?? null;
    $paymentStatus = $rental->payment_status ?? 'unpaid';
    $paymentLabel = match($paymentStatus) {
        'paid' => 'Lunas',
        'partial' => 'Sebagian',
        'overdue' => 'Terlambat',
        default => 'Belum Bayar',
    };
    $paymentVariant = match($paymentStatus) {
        'paid' => 'paid',
        'partial' => 'partial',
        'overdue' => 'overdue',
        default => 'unpaid',
    };
    $deposit = $rental->guarantees->where('type', 'deposit')->sum('deposit_amount');
    $lateFee = $rental->late_fee ?? 0;
    $damageFee = $rental->items->sum('damage_fee') ?? 0;
@endphp

@include('rentals.partials.doc-brand-header', [
    'docLabel' => 'INVOICE',
    'docNumberValue' => $rental->invoice_number,
    'docDateValue' => optional($rental->rental_date)->format('d M Y'),
    'qrRoute' => \Illuminate\Support\Facades\URL::temporarySignedRoute('invoice.public', now()->addDays(30), ['rental' => $rental->id]),
    'companyAddress' => $rental->branch->address ?? '-',
    'companyPhone' => $rental->branch->phone ?? '-',
    'companyWhatsApp' => $rental->branch->phone ?? '-',
    'companyInstagram' => '@sewajas',
    'status' => $paymentStatus,
    'badgeText' => $paymentLabel,
    'variant' => $paymentVariant,
])

@include('rentals.partials.doc-customer-info', [
    'customer' => $rental->customer,
    'rental' => $rental,
])

<div style="margin-top:8px;">
    <div class="section-title" style="padding-left:8px; margin-bottom:6px;">Rental Items</div>
    @include('rentals.partials.doc-item-list', ['items' => $rental->items])
</div>

<table style="width:100%; border-collapse:collapse; margin-top:6px;">
    <tr>
        <td style="width:50%; vertical-align:top; padding-right:4px;">
            @include('rentals.partials.doc-payment-info', ['rental' => $rental])
            @if($rental->fine_status === 'unpaid' || $rental->fine_status === 'partial')
                <div style="margin-top:4px; padding:4px 8px; background:#FEF3C7; border-left:2px solid #F59E0B; border-radius:4px; font-size:9px;">
                    <span style="font-weight:700; color:#92400E;">STATUS DENDA: {{ match($rental->fine_status) { 'unpaid' => 'BELUM DIBAYAR', 'partial' => 'SEBAGIAN', default => strtoupper($rental->fine_status) } }}</span>
                    @if($rental->fine_amount > 0)
                        <span style="color:#92400E; margin-left:4px;">Rp {{ number_format($rental->fine_amount, 0, ',', '.') }}</span>
                    @endif
                </div>
            @endif
        </td>
        <td style="width:50%; vertical-align:top; padding-left:4px;">
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
        </td>
    </tr>
</table>

@include('rentals.partials.doc-payments-history', ['payments' => $rental->payments])

<div style="margin-top:6px;">
    @include('rentals.partials.doc-notes', [
        'notes' => 'Dokumen ini dibuat secara otomatis oleh sistem dan tidak memerlukan tanda tangan. QR dapat digunakan untuk verifikasi invoice.',
    ])
</div>

@php
    $printedBy = auth()->user()?->name ?? ($rental->createdBy?->name ?? \App\Services\SettingsService::get('app_name', 'SewaJas'));
    $printedAt = now()->format('d M Y');
@endphp
@include('rentals.partials.doc-premium-footer', ['printedBy' => $printedBy, 'printedAt' => $printedAt])
