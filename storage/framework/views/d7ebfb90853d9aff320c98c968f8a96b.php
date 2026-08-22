<?php $__env->startSection('title', $branch->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('branches.index')); ?>" class="btn-secondary p-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)"><?php echo e($branch->name); ?></h1>
                <p class="text-sm mt-0.5" style="color: var(--text-soft)">Detail cabang</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('branches.edit', $branch)); ?>" class="btn-primary">
                <i data-lucide="pencil" class="w-4 h-4"></i> Edit
            </a>
<button type="button" onclick="confirmDelete('deleteBranchForm', 'cabang <?php echo e($branch->name); ?>')" class="btn-secondary text-red-500 hover:bg-red-50">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus
            </button>
        </div>
    </div>

    <div class="card p-6 space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Nama Cabang</p>
                <p class="font-semibold" style="color:var(--text-dark)"><?php echo e($branch->name); ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Kode</p>
                <span class="badge badge-gold text-sm"><?php echo e($branch->code); ?></span>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Telepon</p>
                <p style="color:var(--text-dark)"><?php echo e($branch->phone ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Email</p>
                <p style="color:var(--text-dark)"><?php echo e($branch->email ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Kota</p>
                <p style="color:var(--text-dark)"><?php echo e($branch->city ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Provinsi</p>
                <p style="color:var(--text-dark)"><?php echo e($branch->province ?? '-'); ?></p>
            </div>
            <div class="col-span-2">
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Alamat</p>
                <p style="color:var(--text-dark)"><?php echo e($branch->address ?? '-'); ?></p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color:var(--text-soft)">Status</p>
                <?php if($branch->is_active): ?>
                    <span class="badge badge-green">Aktif</span>
                <?php else: ?>
                    <span class="badge badge-gray">Nonaktif</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="border-t pt-4 grid grid-cols-3 gap-4" style="border-color:var(--border)">
            <div class="text-center">
                <p class="text-2xl font-bold" style="color:var(--primary)"><?php echo e($branch->users_count); ?></p>
                <p class="text-xs mt-1" style="color:var(--text-soft)">Pengguna</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold" style="color:var(--primary)"><?php echo e($branch->products_count); ?></p>
                <p class="text-xs mt-1" style="color:var(--text-soft)">Produk</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold" style="color:var(--primary)"><?php echo e($branch->rentals_count); ?></p>
                <p class="text-xs mt-1" style="color:var(--text-soft)">Total Rental</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<form id="deleteBranchForm" method="POST" action="<?php echo e(route('branches.destroy', $branch)); ?>" class="hidden">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>
<?php $__env->startPush('scripts'); ?>
<script>
lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/branches/show.blade.php ENDPATH**/ ?>