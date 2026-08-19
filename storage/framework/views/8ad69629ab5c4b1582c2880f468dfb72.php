<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Print</title>
    <style>
        body { font-family: monospace; width: 80mm; margin: auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="text-center">
        <h2>RENTAL JAS</h2>
        <p><?php echo e(optional($rental->branch)->name ?? '-'); ?></p>
        <p><?php echo e($rental->invoice_number); ?></p>
    </div>
    <hr>
    <p>Customer: <?php echo e(optional($rental->customer)->name ?? '-'); ?></p>
    <p>Tgl: <?php echo e(optional($rental->rental_date)?->format('d/m/Y')); ?></p>
    <hr>
    <?php $__currentLoopData = $rental->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p><?php echo e($item->product_name); ?> x <?php echo e($item->quantity); ?></p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <hr>
    <p class="text-right">TOTAL: Rp <?php echo e(number_format(($rental->total_amount ?? 0), 0, ',', '.')); ?></p>
</body>
</html>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/thermal.blade.php ENDPATH**/ ?>