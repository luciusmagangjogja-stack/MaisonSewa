

<?php if(session('success')): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-start gap-3 px-4 py-3 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 shadow-sm">
        <i data-lucide="check-circle" class="mt-0.5 h-5 w-5 text-emerald-700"></i>
        <span class="text-sm font-semibold"><?php echo e(session('success')); ?></span>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-start gap-3 px-4 py-3 rounded-2xl border border-red-200 bg-red-50 text-red-800 shadow-sm">
        <i data-lucide="alert-circle" class="mt-0.5 h-5 w-5 text-red-700"></i>
        <span class="text-sm font-semibold"><?php echo e(session('error')); ?></span>
    </div>
<?php endif; ?>

<?php if(session('warning')): ?>
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-start gap-3 px-4 py-3 rounded-2xl border border-amber-200 bg-amber-50 text-amber-800 shadow-sm">
        <i data-lucide="alert-triangle" class="mt-0.5 h-5 w-5 text-amber-700"></i>
        <span class="text-sm font-semibold"><?php echo e(session('warning')); ?></span>
    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 text-red-800 p-4 shadow-sm">
        <div class="flex items-center gap-2 text-sm font-bold">
            <i data-lucide="alert-circle" class="h-5 w-5"></i>
            Periksa kembali input berikut
        </div>
        <ul class="mt-2 space-y-1 text-sm">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/components/flash-messages.blade.php ENDPATH**/ ?>