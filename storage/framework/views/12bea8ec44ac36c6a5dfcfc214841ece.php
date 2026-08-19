<?php
    $payments = $payments ?? collect();
?>

<?php if($payments->count() > 0): ?>
    <div style="margin-top: 16px;">
        <div class="section-title" style="margin-bottom: 10px;">Payment History</div>

        <table class="items" style="border-radius:16px;">
            <thead>
                <tr>
                    <th style="width:110px;">Nomor</th>
                    <th>Metode</th>
                    <th style="width:180px;">Tanggal</th>
                    <th style="width:160px; text-align:right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td data-label="Nomor"><?php echo e($pay->payment_number); ?></td>
                    <td data-label="Metode">
                        <span style="display:inline-flex; align-items:center; padding:6px 14px; border-radius:999px; border:1px solid rgba(201,168,76,.22); background: rgba(250,246,240,.75); font-weight:700; font-size:11px; text-transform:uppercase; letter-spacing:0.06em;">
                            <?php echo e(ucfirst($pay->method)); ?>

                        </span>
                    </td>
                    <td data-label="Tanggal"><?php echo e(optional($pay->paid_at)->format('d M Y H:i')); ?></td>
                    <td data-label="Jumlah" style="text-align:right; font-weight:900;">Rp <?php echo e(number_format($pay->amount, 0, ',', '.')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-payments-history.blade.php ENDPATH**/ ?>