<?php $__env->startSection('title', 'Tambah Cabang'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('branches.index')); ?>" class="btn-secondary p-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Tambah Cabang</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Daftarkan cabang baru</p>
        </div>
    </div>

    <div class="card p-6">
        <form method="POST" action="<?php echo e(route('branches.store')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Nama Cabang <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>"
                        class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="cth: Cabang Sleman">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Kode Cabang <span class="text-red-500">*</span></label>
                    <input type="text" name="code" value="<?php echo e(old('code')); ?>"
                        class="form-input <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="cth: SLM01" style="text-transform:uppercase">
                    <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">No. Telepon</label>
                    <input type="text" name="phone" value="<?php echo e(old('phone')); ?>"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        class="form-input" placeholder="cth: 0274-123456">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>"
                        class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="cabang@jasrental.id">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Kota</label>
                    <input type="text" name="city" value="<?php echo e(old('city')); ?>"
                        class="form-input" placeholder="cth: Yogyakarta">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Provinsi</label>
                    <input type="text" name="province" value="<?php echo e(old('province')); ?>"
                        class="form-input" placeholder="cth: DI Yogyakarta">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Alamat Lengkap</label>
                    <textarea name="address" rows="3"
                        class="form-input"
                        placeholder="Alamat lengkap cabang"><?php echo e(old('address')); ?></textarea>
                </div>

                <div class="sm:col-span-2 flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                        class="w-4 h-4 rounded">
                    <label for="is_active" class="text-sm font-medium" style="color:var(--text-dark)">Cabang Aktif</label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan
                </button>
                <a href="<?php echo e(route('branches.index')); ?>" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/branches/create.blade.php ENDPATH**/ ?>