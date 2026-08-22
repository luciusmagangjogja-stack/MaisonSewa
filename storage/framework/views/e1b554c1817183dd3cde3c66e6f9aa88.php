<?php $__env->startSection('title', 'Edit Kategori'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('categories.index')); ?>" class="btn-secondary p-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Edit Kategori</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)"><?php echo e($category->name); ?></p>
        </div>
    </div>

    <div class="card p-6">
        <form method="POST" action="<?php echo e(route('categories.update', $category)); ?>" class="space-y-4">
            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="<?php echo e(old('name', $category->name)); ?>"
                    class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
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
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" value="<?php echo e(old('slug', $category->slug)); ?>"
                    class="form-input <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Icon (Lucide)</label>
                <input type="text" name="icon" value="<?php echo e(old('icon', $category->icon)); ?>"
                    class="form-input" placeholder="cth: briefcase, award, shirt">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Urutan Tampil</label>
                <input type="number" name="sort_order" value="<?php echo e(old('sort_order', $category->sort_order)); ?>"
                    class="form-input" min="0">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    <?php echo e(old('is_active', $category->is_active) ? 'checked' : ''); ?>

                    class="w-4 h-4 rounded">
                <label for="is_active" class="text-sm font-medium" style="color:var(--text-dark)">Kategori Aktif</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> Perbarui
                </button>
                <a href="<?php echo e(route('categories.index')); ?>" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/categories/edit.blade.php ENDPATH**/ ?>