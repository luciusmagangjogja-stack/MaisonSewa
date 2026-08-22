<?php
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

    $createdByName = optional($rental->createdBy)->name ?? \App\Services\SettingsService::get('app_name', 'SewaJas');
    $processedByName = optional($rental->returnedBy)->name ?? null;

    $appName = \App\Services\SettingsService::get('app_name', 'SewaJas');

    $companyAddress = \App\Services\SettingsService::get('company_address');
    $companyPhone = \App\Services\SettingsService::get('company_phone');
    $companyWebsite = \App\Services\SettingsService::get('company_website');

    $qrBase64 = null;
    try {
        $qrRoute = route('rentals.show', $rental);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(160)->generate($qrRoute);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode((string) $qrSvg);
    } catch (\Throwable $e) {
        $qrBase64 = null;
    }
?>

<div class="receipt-compact-wrapper">
    <div class="receipt-compact-card">

        
        <div class="receipt-header">
            <div class="receipt-header-top">
                <div class="receipt-app-name"><?php echo e($appName); ?></div>
                <div class="receipt-header-label">RECEIPT</div>
            </div>
            <div class="receipt-header-meta">
                <span><?php echo e($receiptNumber); ?></span>
                <span class="receipt-meta-sep">|</span>
                <span><?php echo e($receiptDate); ?></span>
                <span class="receipt-brand">MaisonSewa</span>
            </div>
        </div>

        
        <div class="receipt-section">
            <div class="receipt-row">
                <span class="receipt-label">Customer</span>
                <span class="receipt-value"><?php echo e($customerName); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Invoice</span>
                <span class="receipt-value"><?php echo e($invoiceNumber); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Sewa</span>
                <span class="receipt-value"><?php echo e($rentalDate); ?></span>
            </div>
            <div class="receipt-row">
                <span class="receipt-label">Kembali</span>
                <span class="receipt-value"><?php echo e($returnDueDate); ?></span>
            </div>
            <?php if($actualReturnDate): ?>
            <div class="receipt-row">
                <span class="receipt-label">Dikembalikan</span>
                <span class="receipt-value"><?php echo e($actualReturnDate); ?></span>
            </div>
            <?php endif; ?>
            <div class="receipt-row receipt-row-status">
                <span class="receipt-label">Status</span>
                <span class="status-badge <?php echo e($statusBadgeClass); ?>"><?php echo e($statusBadge); ?></span>
            </div>
        </div>

        
        <div class="receipt-section">
            <div class="receipt-section-title">Produk yang Disewa</div>
            <div class="receipt-items">
                <?php $__currentLoopData = $rental->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="receipt-item">
                        <span class="receipt-item-name"><?php echo e(optional($item->product)->name ?? 'Produk #' . $item->product_id); ?></span>
                        <span class="receipt-item-qty">x<?php echo e($item->quantity); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="receipt-section receipt-qr-section">
            <div class="receipt-qr-wrapper">
                <?php if(!empty($qrBase64)): ?>
                    <img src="<?php echo e($qrBase64); ?>" alt="QR Code" class="receipt-qr-image">
                <?php endif; ?>
            </div>
            <div class="receipt-qr-label">Scan untuk detail rental</div>
        </div>

        
        <div class="receipt-section receipt-petugas-section">
            <div class="receipt-row">
                <span class="receipt-label">Dibuat oleh</span>
                <span class="receipt-value"><?php echo e($createdByName); ?></span>
            </div>
            <?php if($processedByName): ?>
            <div class="receipt-row">
                <span class="receipt-label">Diproses oleh</span>
                <span class="receipt-value"><?php echo e($processedByName); ?></span>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="receipt-total-section">
            <div class="receipt-total-row">
                <span class="receipt-total-label">Total</span>
                <span class="receipt-total-value">Rp <?php echo e(number_format($totalAmount, 0, ',', '.')); ?></span>
            </div>
        </div>

        
        <div class="receipt-footer">
            <?php if($companyAddress || $companyPhone || $companyWebsite): ?>
            <div class="receipt-company-info">
                <?php if($companyAddress): ?>
                <div class="receipt-company-line"><?php echo e($companyAddress); ?></div>
                <?php endif; ?>
                <div class="receipt-company-line">
                    <?php if($companyPhone): ?>
                    <?php echo e($companyPhone); ?>

                    <?php endif; ?>
                </div>
                <?php if($companyWebsite): ?>
                <div class="receipt-company-line"><?php echo e($companyWebsite); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-parts-receipt.blade.php ENDPATH**/ ?>