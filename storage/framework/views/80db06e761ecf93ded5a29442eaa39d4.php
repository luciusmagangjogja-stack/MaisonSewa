

<?php $__env->startSection('title', 'Pengaturan'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Pengaturan</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Konfigurasi denda dan durasi sewa default</p>
        </div>
    </div>

    
    <?php echo $__env->make('components.flash-messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="card">
        <form method="POST" action="<?php echo e(route('settings.update')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>

            <?php
                $finePerDay = optional($settings->firstWhere('key', 'fine_per_day'))->value;
                $durationDays = optional($settings->firstWhere('key', 'rental_duration_days'))->value;
            ?>

            <div>
                <label class="block text-sm font-medium" style="color: var(--text-soft)">Denda per Hari Telat (Rp)</label>
                <div class="mt-1">
                    <input
                        type="number"
                        name="fine_per_day"
                        min="0"
                        step="1"
                        value="<?php echo e($finePerDay ?? 0); ?>"
                        class="form-input"
                        required
                    >
                </div>
                <?php $__errorArgs = ['fine_per_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-sm mt-1" style="color: #DC2626"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-medium" style="color: var(--text-soft)">Durasi Sewa Default (hari)</label>
                <div class="mt-1">
                    <input
                        type="number"
                        name="rental_duration_days"
                        min="0"
                        step="1"
                        value="<?php echo e($durationDays ?? 3); ?>"
                        class="form-input"
                        required
                    >
                </div>
                <?php $__errorArgs = ['rental_duration_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-sm mt-1" style="color: #DC2626"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="<?php echo e(route('dashboard')); ?>" class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan
                </button>
            </div>
        </form>
    </div>

</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/settings/index.blade.php ENDPATH**/ ?>