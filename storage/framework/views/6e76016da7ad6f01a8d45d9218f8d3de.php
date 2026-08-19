<?php
    $customer = $customer ?? null;
    $rental = $rental ?? null;
    $createdBy = $createdBy ?? null;
    $branch = $branch ?? null;

    $rentalStatus = $rental->rental_status ?? 'waiting';
    $rentalStatusBadge = match($rentalStatus) {
        'returned' => 'RETURNED',
        'active', 'waiting' => 'ON RENT',
        'overdue' => 'LATE RETURN',
        'cancelled' => 'CANCELLED',
        default => strtoupper($rentalStatus),
    };
    $rentalStatusBadgeClass = match($rentalStatus) {
        'returned' => 'status-paid',
        'active', 'waiting' => 'status-paid',
        'overdue' => 'status-overdue',
        'cancelled' => 'status-unpaid',
        default => 'status-unpaid',
    };
?>

<div style="display:table; width:100%; border-collapse:separate; border-spacing:0 12px; margin-bottom:14px;">
    <!-- Customer Card -->
    <div style="display:table-cell; width:50%; vertical-align:top; padding-right:8px;">
        <div class="info-card">
            <div class="info-card-title">Customer</div>
            <div class="info-row">
                <span class="info-label">Nama</span>
                <span class="info-value"><?php echo e(optional($customer)->name ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value">
                    <?php
                        $phone = optional($customer)->phone ?? '-';
                        if ($phone !== '-' && preg_match('/^(\d{4})(\d{4})(\d{4})$/', $phone, $matches)) {
                            $phone = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
                        }
                    ?>
                    <?php echo e($phone); ?>

                </span>
            </div>
            <?php if(!empty($customer->address)): ?>
            <div class="info-row">
                <span class="info-label">Alamat</span>
                <span class="info-value"><?php echo e($customer->address); ?></span>
            </div>
            <?php endif; ?>
            <?php if(!empty($customer->email)): ?>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?php echo e($customer->email); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rental Card -->
    <div style="display:table-cell; width:50%; vertical-align:top; padding-left:8px;">
        <div class="info-card">
            <div class="info-card-title">Rental Detail</div>
            <div class="info-row">
                <span class="info-label">Rental ID</span>
                <span class="info-value">#<?php echo e($rental->id ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value"><?php echo e(optional($rental->rental_date)->format('d M Y') ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Kembali</span>
                <span class="info-value"><?php echo e(optional($rental->return_due_date)->format('d M Y') ?? '-'); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Durasi</span>
                <span class="info-value"><?php echo e($rental->duration_days ?? 0); ?> hari</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode</span>
                <span class="info-value"><?php echo e(ucfirst($rental->payment_method ?? '-')); ?></span>
            </div>
            <div class="info-row" style="margin-top:6px; padding-top:6px; border-top:1px solid #E5E7EB;">
                <span class="info-label">Status</span>
                <span class="status-badge <?php echo e($rentalStatusBadgeClass); ?>"><?php echo e($rentalStatusBadge); ?></span>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-customer-info.blade.php ENDPATH**/ ?>