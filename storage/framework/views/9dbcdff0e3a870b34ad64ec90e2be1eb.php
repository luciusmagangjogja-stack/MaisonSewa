<?php $__env->startSection('title', 'Kategori'); ?>
<?php $__env->startSection('page-title', 'Kategori'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Kategori</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Kelola kategori produk jas</p>
        </div>
<?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isSuperAdmin()): ?>
        <a href="<?php echo e(route('categories.create')); ?>" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Kategori
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <div class="card overflow-hidden">
        <?php if($categories->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4"
                 style="background: var(--secondary)">
                <i data-lucide="tag" class="w-8 h-8" style="color:var(--primary)"></i>
            </div>
            <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada kategori</p>
            <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Tambahkan kategori produk pertama</p>
            <a href="<?php echo e(route('categories.create')); ?>" class="btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kategori
            </a>
        </div>
        <?php else: ?>
        
        <div class="hidden md:block">
            <table class="elegant-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Kategori</th>
                        <th class="text-left">Slug</th>
                        <th class="text-center" style="text-align:center">Icon</th>
                        <th class="text-center" style="text-align:center">Urutan</th>
                        <th class="text-center" style="text-align:center">Jumlah Produk</th>
                        <th class="text-center" style="text-align:center">Status</th>
                        <th class="text-center" style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <p class="font-semibold text-sm" style="color:var(--text-dark)"><?php echo e($category->name); ?></p>
                        </td>
                        <td>
                            <span class="text-xs font-mono px-2 py-1 rounded-lg" style="background:var(--secondary); color:var(--text-soft)">
                                <?php echo e($category->slug); ?>

                            </span>
                        </td>
                        <td class="text-center" style="text-align:center">
                            <?php if($category->icon): ?>
                            <div class="flex justify-center w-full">
                                <i data-lucide="<?php echo e($category->icon); ?>" class="w-5 h-5" style="color:var(--primary)"></i>
                            </div>
                            <?php else: ?>
                            <span class="text-xs" style="color:var(--text-soft)">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center" style="text-align:center">
                            <span class="text-sm font-medium" style="color:var(--text-soft)"><?php echo e($category->sort_order); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-gold"><?php echo e($category->products_count); ?> produk</span>
                        </td>
                        <td class="text-center">
                            <?php if($category->is_active): ?>
                                <span class="badge badge-green">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-gray">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <?php if(auth()->guard()->check()): ?>
                                    <?php if(auth()->user()->isSuperAdmin()): ?>
                                    <a href="<?php echo e(route('categories.edit', $category)); ?>"
                                       class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4" style="color:var(--primary)"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('categories.destroy', $category)); ?>" id="deleteCategory_<?php echo e($category->id); ?>" class="hidden">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    </form>
                                    <button type="button" onclick="confirmDelete('deleteCategory_<?php echo e($category->id); ?>', 'kategori <?php echo e($category->name); ?>')"
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
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card border rounded-xl p-4 space-y-3">
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:var(--secondary)">
                        <?php if($category->icon): ?>
                            <i data-lucide="<?php echo e($category->icon); ?>" class="w-5 h-5" style="color:var(--primary)"></i>
                        <?php else: ?>
                            <i data-lucide="tag" class="w-5 h-5" style="color:var(--text-soft)"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color:var(--text-dark)"><?php echo e($category->name); ?></p>
                    </div>
                    <?php if($category->is_active): ?>
                        <span class="badge badge-green text-xs">Aktif</span>
                    <?php else: ?>
                        <span class="badge badge-gray text-xs">Nonaktif</span>
                    <?php endif; ?>
                </div>

                
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs" style="color:var(--text-soft)">
                    <span class="font-mono px-2 py-0.5 rounded-md" style="background:var(--secondary)">
                        <?php echo e($category->slug); ?>

                    </span>
                    <span>Urutan: <strong class="font-medium" style="color:var(--text-dark)"><?php echo e($category->sort_order); ?></strong></span>
                    <span><?php echo e($category->products_count); ?> produk</span>
                </div>

                
                <div class="flex items-center gap-2 pt-2 border-t" style="border-color:var(--border)">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <a href="<?php echo e(route('categories.edit', $category)); ?>"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Edit
                        </a>
                        <form method="POST" action="<?php echo e(route('categories.destroy', $category)); ?>" id="deleteCategoryMobile_<?php echo e($category->id); ?>" class="hidden">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        </form>
                        <button type="button"
                                onclick="confirmDelete('deleteCategoryMobile_<?php echo e($category->id); ?>', 'kategori <?php echo e($category->name); ?>')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] text-red-600 hover:bg-red-50">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                        </button>
                        <?php else: ?>
                        <a href="<?php echo e(route('categories.show', $category)); ?>"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="eye" class="w-4 h-4"></i> Lihat Detail
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?php echo e(route('categories.show', $category)); ?>"
                           class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                           style="background:var(--secondary); color:var(--text-dark)">
                            <i data-lucide="eye" class="w-4 h-4"></i> Lihat Detail
                        </a>
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

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/categories/index.blade.php ENDPATH**/ ?>