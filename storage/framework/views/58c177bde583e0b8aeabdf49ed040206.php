<?php $__env->startSection('title', 'Kelola Cabang'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Kelola Cabang</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Manajemen cabang toko JasRental</p>
        </div>
<?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isSuperAdmin()): ?>
        <a href="<?php echo e(route('branches.create')); ?>" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Cabang
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="text-xs font-medium uppercase tracking-wide" style="color:var(--text-soft)">Total Cabang</p>
            <p class="text-2xl font-bold mt-1" style="color:var(--text-dark)"><?php echo e($branches->count()); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium uppercase tracking-wide" style="color:var(--text-soft)">Cabang Aktif</p>
            <p class="text-2xl font-bold mt-1 text-green-600"><?php echo e($branches->where('is_active', true)->count()); ?></p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium uppercase tracking-wide" style="color:var(--text-soft)">Cabang Nonaktif</p>
            <p class="text-2xl font-bold mt-1 text-red-500"><?php echo e($branches->where('is_active', false)->count()); ?></p>
        </div>
    </div>

    
    <div class="card overflow-hidden">
        <?php if($branches->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--secondary)">
                <i data-lucide="store" class="w-8 h-8" style="color:var(--primary)"></i>
            </div>
            <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada cabang</p>
            <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Tambahkan cabang pertama</p>
            <a href="<?php echo e(route('branches.create')); ?>" class="btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Cabang
            </a>
        </div>
        <?php else: ?>
        
        <div class="hidden md:block">
        <table class="elegant-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Cabang</th>
                    <th class="text-left">Kode</th>
                    <th class="text-left">Kota</th>
                    <th class="text-left">Kontak</th>
                    <th class="text-center">Pengguna</th>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <p class="font-semibold text-sm" style="color:var(--text-dark)"><?php echo e($branch->name); ?></p>
                        <?php if($branch->address): ?>
                        <p class="text-xs mt-0.5 line-clamp-1" style="color:var(--text-soft)"><?php echo e($branch->address); ?></p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="text-xs font-mono px-2 py-1 rounded-lg font-bold" style="background:var(--secondary); color:var(--primary)">
                            <?php echo e($branch->code); ?>

                        </span>
                    </td>
                    <td>
                        <p class="text-sm" style="color:var(--text-dark)"><?php echo e($branch->city ?? '-'); ?></p>
                        <p class="text-xs" style="color:var(--text-soft)"><?php echo e($branch->province ?? ''); ?></p>
                    </td>
                    <td>
                        <?php if($branch->phone): ?>
                        <p class="text-sm" style="color:var(--text-dark)"><?php echo e($branch->phone); ?></p>
                        <?php endif; ?>
                        <?php if($branch->email): ?>
                        <p class="text-xs" style="color:var(--text-soft)"><?php echo e($branch->email); ?></p>
                        <?php endif; ?>
                        <?php if(!$branch->phone && !$branch->email): ?>
                        <span class="text-xs" style="color:var(--text-soft)">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-blue"><?php echo e($branch->users_count); ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-gold"><?php echo e($branch->products_count); ?></span>
                    </td>
                    <td class="text-center">
                        <?php if($branch->is_active): ?>
                            <span class="badge badge-green">Aktif</span>
                        <?php else: ?>
                            <span class="badge badge-gray">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="<?php echo e(route('branches.show', $branch)); ?>"
                               class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Detail">
                                <i data-lucide="eye" class="w-4 h-4" style="color:var(--text-soft)"></i>
                            </a>
<?php if(auth()->guard()->check()): ?>
                            <?php if(auth()->user()->isSuperAdmin()): ?>
                            <a href="<?php echo e(route('branches.edit', $branch)); ?>"
                               class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4" style="color:var(--primary)"></i>
                            </a>
                            <form method="POST" action="<?php echo e(route('branches.destroy', $branch)); ?>" id="deleteBranch_<?php echo e($branch->id); ?>" class="hidden">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            </form>
                            <button type="button" onclick="confirmDelete('deleteBranch_<?php echo e($branch->id); ?>', 'cabang <?php echo e($branch->name); ?>')"
                                    class="p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i>
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>

        
        <div class="md:hidden space-y-3">
            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card border rounded-xl p-4 space-y-3">
                
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color:var(--text-dark)"><?php echo e($branch->name); ?></p>
                        <span class="text-xs font-mono px-2 py-0.5 rounded-md" style="background:var(--secondary); color:var(--primary)">
                            <?php echo e($branch->code); ?>

                        </span>
                    </div>
                    <?php if($branch->is_active): ?>
                        <span class="badge badge-green text-xs">Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-gray text-xs">Nonaktif</span>
                    <?php endif; ?>
                </div>

                
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs" style="color:var(--text-soft)">
                    <span>
                        <strong class="font-medium" style="color:var(--text-dark)"><?php echo e($branch->city ?? '-'); ?></strong>
                        <?php if($branch->province): ?>
                            <span>, <?php echo e($branch->province); ?></span>
                        <?php endif; ?>
                    </span>
                    <span>
                        <?php if($branch->phone): ?>
                            <strong class="font-medium" style="color:var(--text-dark)"><?php echo e($branch->phone); ?></strong>
                        <?php elseif($branch->email): ?>
                            <?php echo e($branch->email); ?>

                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </span>
                    <span><?php echo e($branch->users_count); ?> Pengguna</span>
                    <span><?php echo e($branch->products_count); ?> Produk</span>
                </div>

                
                <div class="flex items-center gap-2 pt-2 border-t" style="border-color:var(--border)">
                    <a href="<?php echo e(route('branches.show', $branch)); ?>"
                       class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                       style="background:var(--secondary); color:var(--text-dark)">
                        <i data-lucide="eye" class="w-4 h-4"></i> Detail
                    </a>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <a href="<?php echo e(route('branches.edit', $branch)); ?>"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                        </a>
                        <form method="POST" action="<?php echo e(route('branches.destroy', $branch)); ?>" id="deleteBranchMobile_<?php echo e($branch->id); ?>" class="hidden">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        </form>
                        <button type="button"
                                onclick="confirmDelete('deleteBranchMobile_<?php echo e($branch->id); ?>', 'cabang <?php echo e($branch->name); ?>')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] text-red-600 hover:bg-red-50">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                        </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/branches/index.blade.php ENDPATH**/ ?>