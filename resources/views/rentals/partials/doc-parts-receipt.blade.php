@php
    $rental = $rental ?? null;
    $receipt = $receipt ?? [];

    $receiptNumber = $receipt['receipt_number'] ?? ('RCPT-' . ($rental->invoice_number ?? '-'));
    $receiptDate = now('Asia/Jakarta')->format('d M Y H:i');

    $customerName = optional($rental->customer)->name ?? '-';
    $invoiceNumber = $rental->invoice_number ?? '-';
    $rentalDate = optional($rental->rental_date)->format('d M Y') ?? '-';
    $returnDueDate = optional($rental->return_due_date)->format('d M Y') ?? '-';
    $actualReturnDate = optional($rental->returned_at)?->format('d M Y H:i');

    $rentalStatus = $rental->rental_status ?? 'waiting';
    $statusBadge = match($rentalStatus) {
        'returned' => 'SELESAI',
        'active' => 'SEDANG DISEWA',
        'overdue' => 'TERLAMBAT',
        'cancelled' => 'DIBATALKAN',
        'waiting' => 'BOOKING',
        default => strtoupper($rentalStatus),
    };
    $statusBadgeClass = match($rentalStatus) {
        'returned' => 'status-paid',
        'active' => 'status-paid',
        'overdue' => 'status-overdue',
        'cancelled' => 'status-unpaid',
        default => 'status-unpaid',
    };

    $totalAmount = (float) ($rental->total_amount ?? 0);

    $createdByName = optional($rental->createdBy)->name ?? \App\Services\SettingsService::get('company_name', 'SewaJas');
    $processedByName = optional($rental->returnedBy)->name ?? null;

    $appName = \App\Services\SettingsService::get('app_name', 'SewaJas');

    $qrBase64 = null;
    try {
        $qrRoute = route('rentals.show', $rental);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(160)->generate($qrRoute);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode((string) $qrSvg);
    } catch (\Throwable $e) {
        $qrBase64 = null;
    }
@endphp

<div class="receipt-compact-wrapper">
    <div class="receipt-compact-card">

        {{-- HEADER --}}
        <div class="receipt-header">
            <div class="receipt-header-top">
                <div class="receipt-app-name">{{ $appName }}</div>
                <div class="receipt-label">RECEIPT</div>
            </div>
            <div class="receipt-header-meta">
                <span>{{ $receiptNumber }}</span>
                <span class="receipt-meta-sep">|</span>
                <span>{{ $receiptDate }}</span>
            </div>
        </div>

        {{-- CUSTOMER + BOOKING --}}
        <div class="receipt-section">
            <div class="receipt-row">
                <span class="receipt-label">Customer</span>
                <span class="receipt-value">{{ $customerName }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Invoice</span>
                <span class="receipt-value">{{ $invoiceNumber }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Sewa</span>
                <span class="receipt-value">{{ $rentalDate }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Kembali</span>
                <span class="receipt-value">{{ $returnDueDate }}</span>
            </div>
            @if($actualReturnDate)
            <div class="receipt-row">
                <span class="receipt-label">Dikembalikan</span>
                <span class="receipt-value">{{ $actualReturnDate }}</span>
            </div>
            @endif
            <div class="receipt-row receipt-row-status">
                <span class="receipt-label">Status</span>
                <span class="status-badge {{ $statusBadgeClass }}">{{ $statusBadge }}</span>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="receipt-section">
            <div class="receipt-section-title">Produk yang Disewa</div>
            <div class="receipt-items">
                @foreach($rental->items as $item)
                    <div class="receipt-item">
                        <span class="receipt-item-name">{{ optional($item->product)->name ?? 'Produk #' . $item->product_id }}</span>
                        <span class="receipt-item-qty">x{{ $item->quantity }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- QR CODE --}}
        <div class="receipt-section receipt-qr-section">
            <div class="receipt-qr-wrapper">
                @if(!empty($qrBase64))
                    <img src="{{ $qrBase64 }}" alt="QR Code" class="receipt-qr-image">
                @endif
            </div>
            <div class="receipt-qr-label">Scan untuk detail rental</div>
        </div>

        {{-- PETUGAS --}}
        <div class="receipt-section receipt-petugas-section">
            <div class="receipt-row">
                <span class="receipt-label">Dibuat oleh</span>
                <span class="receipt-value">{{ $createdByName }}</span>
            </div>
            @if($processedByName)
            <div class="receipt-row">
                <span class="receipt-label">Diproses oleh</span>
                <span class="receipt-value">{{ $processedByName }}</span>
            </div>
            @endif
        </div>

        {{-- TOTAL --}}
        <div class="receipt-total-section">
            <div class="receipt-total-row">
                <span class="receipt-total-label">Total</span>
                <span class="receipt-total-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="receipt-footer">
            <div class="receipt-footer-text">
                Receipt ini dibuat otomatis oleh {{ $appName }} — {{ $receiptDate }}
            </div>
            <div class="receipt-footer-note">
                Scan QR untuk melihat detail rental di sistem
            </div>
        </div>

    </div>
</div>
