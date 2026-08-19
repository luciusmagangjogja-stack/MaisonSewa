<?php
    $subtotal = $subtotal ?? 0;
    $discount = $discount ?? 0;
    $deposit = $deposit ?? 0;
    $additionalCharges = $additionalCharges ?? 0;
    $lateFee = $lateFee ?? 0;
    $damageFee = $damageFee ?? 0;
    $totalAmount = $totalAmount ?? 0;
    $paidAmount = $paidAmount ?? 0;
    $changeAmount = $changeAmount ?? 0;
    $remainingBalance = $remainingBalance ?? 0;
    $grandTotal = max($totalAmount, 0);
    $totalPaid = max($paidAmount, 0);
    $change = max($changeAmount, 0);
    $guaranteeType = $guaranteeType ?? null;
?>

<div class="summary-card">
    <div class="summary-card-title">Payment Summary</div>
    <table class="summary-table" style="width:100%; border-collapse:collapse;">
        <tr>
            <td class="summary-label">Subtotal Rental</td>
            <td class="summary-value">Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></td>
        </tr>
        <?php if($discount > 0): ?>
        <tr>
            <td class="summary-label">Discount</td>
            <td class="summary-value" style="color:#B91C1C;">- Rp <?php echo e(number_format($discount, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($guaranteeType === 'deposit' && $deposit > 0): ?>
        <tr>
            <td class="summary-label">Deposit Dibayar</td>
            <td class="summary-value">Rp <?php echo e(number_format($deposit, 0, ',', '.')); ?></td>
        </tr>
        <?php elseif($guaranteeType === 'ktp'): ?>
        <tr>
            <td class="summary-label">Jaminan</td>
            <td class="summary-value">KTP</td>
        </tr>
        <?php elseif($guaranteeType === 'sim'): ?>
        <tr>
            <td class="summary-label">Jaminan</td>
            <td class="summary-value">SIM</td>
        </tr>
        <?php elseif($guaranteeType === 'custom'): ?>
        <tr>
            <td class="summary-label">Jaminan</td>
            <td class="summary-value">Custom</td>
        </tr>
        <?php endif; ?>
        <?php if($lateFee > 0 && $damageFee > 0): ?>
        <tr>
            <td class="summary-label">Denda Telat</td>
            <td class="summary-value" style="color:#B91C1C;">Rp <?php echo e(number_format($lateFee, 0, ',', '.')); ?></td>
        </tr>
        <tr>
            <td class="summary-label">Biaya Kerusakan</td>
            <td class="summary-value" style="color:#B91C1C;">Rp <?php echo e(number_format($damageFee, 0, ',', '.')); ?></td>
        </tr>
        <?php elseif($lateFee > 0): ?>
        <tr>
            <td class="summary-label">Denda Telat</td>
            <td class="summary-value" style="color:#B91C1C;">Rp <?php echo e(number_format($lateFee, 0, ',', '.')); ?></td>
        </tr>
        <?php elseif($damageFee > 0): ?>
        <tr>
            <td class="summary-label">Biaya Kerusakan</td>
            <td class="summary-value" style="color:#B91C1C;">Rp <?php echo e(number_format($damageFee, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <tr style="border-top:1px solid #E5E7EB;">
            <td class="summary-label" style="padding-top:6px; padding-bottom:6px; font-weight:700;">Grand Total</td>
            <td class="summary-value" style="padding-top:6px; padding-bottom:6px; font-weight:800; color:#1E40AF; font-size:20px;">Rp <?php echo e(number_format($grandTotal, 0, ',', '.')); ?></td>
        </tr>
        <?php if($totalPaid > 0): ?>
        <tr style="border-top:1px solid #E5E7EB;">
            <td class="summary-label" style="padding-top:6px; color:#15803D; font-weight:700;">Customer Paid</td>
            <td class="summary-value" style="padding-top:6px; color:#15803D; font-weight:700;">Rp <?php echo e(number_format($totalPaid, 0, ',', '.')); ?></td>
        </tr>
        <?php if($change > 0): ?>
        <tr>
            <td class="summary-label" style="color:#15803D;">Change</td>
            <td class="summary-value" style="color:#15803D;">Rp <?php echo e(number_format($change, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($remainingBalance > 0): ?>
        <tr>
            <td class="summary-label" style="color:#B91C1C; font-weight:700;">Remaining Balance</td>
            <td class="summary-value" style="color:#B91C1C; font-weight:700;">Rp <?php echo e(number_format($remainingBalance, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($changeAmount > 0): ?>
        <tr style="border-top:1px solid #E5E7EB;">
            <td class="summary-label" style="padding-top:6px; color:#D97706; font-weight:700;">Kembalian</td>
            <td class="summary-value" style="padding-top:6px; color:#D97706; font-weight:700;">Rp <?php echo e(number_format($changeAmount, 0, ',', '.')); ?></td>
        </tr>
        <?php endif; ?>
        <?php endif; ?>
    </table>
</div>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-price-summary.blade.php ENDPATH**/ ?>