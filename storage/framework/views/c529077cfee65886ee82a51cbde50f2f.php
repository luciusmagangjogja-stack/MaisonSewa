<?php
    $rental = $rental ?? null;
    $paymentMethod = $rental->payment_method ?? null;
    $paymentStatus = $rental->payment_status ?? 'unpaid';
    $changeAmount = $rental->change_amount ?? 0;

    $methodLabel = match($paymentMethod) {
        'cash' => 'Tunai',
        'transfer' => 'Transfer',
        'qris' => 'QRIS',
        default => $paymentMethod ?? '-',
    };
    $statusLabel = match($paymentStatus) {
        'paid' => 'Lunas',
        'partial' => 'Sebagian',
        'overdue' => 'Terlambat',
        default => 'Belum Bayar',
    };
    $statusBadgeClass = match($paymentStatus) {
        'paid' => 'status-paid',
        'partial' => 'status-partial',
        'overdue' => 'status-overdue',
        default => 'status-unpaid',
    };
?>

<div class="payment-card">
    <div class="payment-card-title">Payment Information</div>
    <div class="payment-row">
        <span class="payment-label">Metode</span>
        <span class="payment-value"><?php echo e($methodLabel); ?></span>
    </div>
    <div class="payment-row">
        <span class="payment-label">Status Sewa</span>
        <span class="status-badge <?php echo e($statusBadgeClass); ?>"><?php echo e($statusLabel); ?></span>
    </div>
    <?php if($rental->fine_status === 'unpaid' || $rental->fine_status === 'partial'): ?>
    <div class="payment-row">
        <span class="payment-label">Status Denda</span>
        <span class="status-badge status-partial"><?php echo e(match($rental->fine_status) { 'unpaid' => 'BELUM DIBAYAR', 'partial' => 'SEBAGIAN', default => strtoupper($rental->fine_status) }); ?></span>
        <?php if($rental->fine_amount > 0): ?>
        <span class="payment-value" style="color:#B91C1C; font-weight:700;">Rp <?php echo e(number_format($rental->fine_amount, 0, ',', '.')); ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if(!empty($rental->payments) && $rental->payments->count() > 0): ?>
        <?php
            $latestPayment = $rental->payments->sortByDesc('paid_at')->first();
        ?>
        <div class="payment-row">
            <span class="payment-label">Paid Date</span>
            <span class="payment-value"><?php echo e(optional($latestPayment->paid_at)->format('d M Y H:i') ?? '-'); ?></span>
        </div>
        <?php if(!empty($latestPayment->reference_number)): ?>
        <div class="payment-row">
            <span class="payment-label">Reference</span>
            <span class="payment-value"><?php echo e($latestPayment->reference_number); ?></span>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if($paymentMethod === 'cash' && $changeAmount > 0): ?>
    <div class="payment-row">
            <span class="payment-label">Cash Change</span>
        <span class="payment-value" style="color:#15803D; font-weight:700;">Rp <?php echo e(number_format($changeAmount, 0, ',', '.')); ?></span>
    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-payment-info.blade.php ENDPATH**/ ?>