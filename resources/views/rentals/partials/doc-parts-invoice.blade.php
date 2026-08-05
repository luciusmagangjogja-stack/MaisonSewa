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
    $deposit = $rental->items->sum(fn($i) => $i->product?->deposit_price ?? 0);
    $additionalCharges = $rental->late_fee ?? 0;
@endphp

@include('rentals.partials.doc-brand-header', [
    'docLabel' => 'INVOICE',
    'docNumberValue' => $rental->invoice_number,
    'docDateValue' => optional($rental->rental_date)->format('d M Y'),
    'qrRoute' => \Illuminate\Support\Facades\URL::temporarySignedRoute('invoice.public', now()->addDays(30), ['rental' => $rental->id]),
    'companyAddress' => $rental->branch->address ?? '-',
    'companyPhone' => $rental->branch->phone ?? '-',
    'companyEmail' => 'support@sewajas.id',
    'companyWebsite' => 'www.sewajas.id',
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

<div style="margin-top:12px;">
    <div class="section-title" style="padding-left:8px;">Rental Items</div>
    @include('rentals.partials.doc-item-list', ['items' => $rental->items])
</div>

<table style="width:100%; border-collapse:separate; border-spacing:0 8px; margin-top:12px;">
    <tr>
        <td style="width:50%; vertical-align:top; padding-right:6px;">
            @include('rentals.partials.doc-payment-info', ['rental' => $rental])
        </td>
        <td style="width:50%; vertical-align:top; padding-left:6px;">
            @include('rentals.partials.doc-price-summary', [
                'subtotal' => $rental->subtotal ?? 0,
                'discount' => $rental->discount ?? 0,
                'deposit' => $deposit ?? 0,
                'additionalCharges' => $additionalCharges ?? 0,
                'lateFee' => $rental->late_fee ?? 0,
                'damageFee' => 0,
                'totalAmount' => $rental->total_amount ?? 0,
                'paidAmount' => $rental->paid_amount ?? 0,
                'changeAmount' => $rental->change_amount ?? 0,
                'remainingBalance' => max(0, ($rental->total_amount ?? 0) - ($rental->paid_amount ?? 0)),
            ])
        </td>
    </tr>
</table>

@include('rentals.partials.doc-payments-history', ['payments' => $rental->payments])

<div style="margin-top:8px;">
    @include('rentals.partials.doc-notes', [
        'notes' => 'Dokumen ini dibuat secara otomatis oleh sistem dan tidak memerlukan tanda tangan. QR dapat digunakan untuk verifikasi invoice.',
    ])
</div>

@php
    $printedBy = auth()->user()?->name ?? ($rental->createdBy?->name ?? 'SewaJas System');
    $printedAt = now()->format('d M Y');
@endphp
@include('rentals.partials.doc-premium-footer', ['printedBy' => $printedBy, 'printedAt' => $printedAt])
