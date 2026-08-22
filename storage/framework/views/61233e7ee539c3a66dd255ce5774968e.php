<?php $__env->startSection('title', 'Dashboard Sales'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>
<?php $__env->startSection('subtitle', 'Selamat datang kembali, ' . auth()->user()->name); ?>

<?php $__env->startSection('content'); ?>
    <?php ($role = 'sales'); ?>
    <?php echo $__env->make('dashboard._dashboard_common', ['role' => $role], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/dashboard/sales.blade.php ENDPATH**/ ?>