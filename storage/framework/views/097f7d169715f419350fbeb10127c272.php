<?php $__env->startSection('title', 'Produk Jas'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-bold text-2xl tracking-tight" style="color: var(--text-dark)">Produk Jas</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Kelola stok dan produk rental</p>
        </div>
<?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isSuperAdmin()): ?>
        <a href="<?php echo e(route('products.create')); ?>" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Produk
        </a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    
    <?php
        $totalProducts = $stats['total'] ?? 0;
        $availableProducts = $stats['available'] ?? 0;
        $maintenanceProducts = $stats['maintenance'] ?? 0;
        $rentedUnits = $stats['rented_units'] ?? 0;
    ?>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Total Produk</p>
                <p class="text-2xl font-bold mt-0.5 leading-tight" style="color:var(--text-dark)"><?php echo e($totalProducts); ?></p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Tersedia</p>
                <p class="text-2xl font-bold mt-0.5 leading-tight text-green-600"><?php echo e($availableProducts); ?></p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: rgba(37, 99, 235, 0.12); color: #1d4ed8;">
                <i data-lucide="shirt" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Sedang Disewa</p>
                <p class="text-2xl font-bold mt-0.5 leading-tight text-blue-600"><?php echo e($rentedUnits); ?></p>
            </div>
        </div>
        <div class="stat-card flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0" style="background: rgba(220, 38, 38, 0.10); color: #dc2626;">
                <i data-lucide="wrench" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Maintenance</p>
                <p class="text-2xl font-bold mt-0.5 leading-tight text-red-500"><?php echo e($maintenanceProducts); ?></p>
            </div>
        </div>
    </div>

    
    <div class="card p-4">
        <form method="GET" action="<?php echo e(route('products.index')); ?>" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                    placeholder="Cari nama, kode produk..."
                    class="form-input" style="padding-left: 44px !important">
            </div>
            <select name="category" class="form-input sm:w-44">
                <option value="">Semua Kategori</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category') == $cat->id ? 'selected' : ''); ?>>
                    <?php echo e($cat->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="status" class="form-input sm:w-40">
                <option value="">Semua Status</option>
                <option value="available"   <?php echo e(request('status') === 'available'   ? 'selected' : ''); ?>>Tersedia</option>
                <option value="rented"      <?php echo e(request('status') === 'rented'      ? 'selected' : ''); ?>>Disewa</option>
                <option value="maintenance" <?php echo e(request('status') === 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
            </select>
            <select name="size" class="form-input sm:w-32">
                <option value="">Semua Ukuran</option>
                <?php $__currentLoopData = ['XS','S','M','L','XL','XXL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($sz); ?>" <?php echo e(request('size') === $sz ? 'selected' : ''); ?>><?php echo e($sz); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()->isSuperAdmin()): ?>
            <select name="branch_id" class="form-input sm:w-44">
                <option value="">Semua Cabang</option>
                <?php $__currentLoopData = $branches ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch_id') == $branch->id ? 'selected' : ''); ?>>
                    <?php echo e($branch->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php endif; ?>
            <?php endif; ?>
            <button type="submit" class="btn-primary whitespace-nowrap">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
            </button>
            <?php if(request()->hasAny(['search','category','status','size'])): ?>
            <a href="<?php echo e(route('products.index')); ?>" class="btn-secondary whitespace-nowrap">
                <i data-lucide="x" class="w-4 h-4"></i> Reset
            </a>
            <?php endif; ?>
        </form>
    </div>

    
    <div class="flex items-center justify-between">
        <p class="text-sm" style="color:var(--text-soft)">
            <?php echo e($products->total()); ?> produk ditemukan
        </p>
        <div class="flex items-center gap-1 p-1 rounded-xl" style="background:var(--secondary)">
            <button id="btn-card"
                onclick="setView('card')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                style="background:var(--card-bg); color:var(--text-dark); box-shadow:0 1px 3px rgba(0,0,0,.08)">
                <i data-lucide="layout-grid" class="w-4 h-4"></i> Kartu
            </button>
            <button id="btn-table"
                onclick="setView('table')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
                style="color:var(--text-soft)">
                <i data-lucide="table" class="w-4 h-4"></i> Tabel
            </button>
        </div>
    </div>

    <?php if($products->isEmpty()): ?>
    <div class="card p-12 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:var(--secondary)">
            <i data-lucide="shirt" class="w-8 h-8" style="color:var(--primary)"></i>
        </div>
        <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada produk</p>
        <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Tambahkan produk jas pertama Anda</p>
        <a href="<?php echo e(route('products.create')); ?>" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Produk
        </a>
    </div>
    <?php else: ?>

    <div id="view-card">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card overflow-hidden group flex flex-col">

                <div class="relative overflow-hidden" style="aspect-ratio: 3/4; background:var(--secondary)">
                    <?php if($product->photo): ?>
                        <img src="<?php echo e($product->photo_url); ?>"
                             alt="<?php echo e($product->name); ?>"
                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center px-3 text-center"
                             style="background: linear-gradient(135deg, rgba(59,130,246,.12), rgba(20,184,166,.10));">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                                 style="background: rgba(59,130,246,.14); border: 1px solid rgba(59,130,246,.18)">
                                <i data-lucide="shirt" class="w-7 h-7" style="color:var(--primary); opacity:.55"></i>
                            </div>
                            <span class="text-xs mt-2" style="color:var(--text-soft)">Tidak ada foto</span>
                        </div>
                    <?php endif; ?>

                    <div class="absolute top-2 left-2">
                        <?php if($product->status === 'available'): ?>
                            <span class="badge badge-green text-xs">Tersedia</span>
                        <?php elseif($product->status === 'rented'): ?>
                            <span class="badge badge-blue text-xs">Disewa</span>
                        <?php elseif($product->status === 'maintenance'): ?>
                            <span class="badge badge-red text-xs">Maintenance</span>
                        <?php else: ?>
                            <span class="badge badge-gray text-xs"><?php echo e($product->status); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="absolute top-2 right-2 flex flex-col gap-1.5 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                        <a href="<?php echo e(route('products.show', $product)); ?>"
                           class="w-9 h-9 rounded-full bg-white flex items-center justify-center hover:scale-110 transition-transform"
                           title="Detail">
                            <i data-lucide="eye" class="w-4 h-4" style="color:var(--text-dark)"></i>
                        </a>
                        <?php if(!auth()->user()->isSales()): ?>
                        <a href="<?php echo e(route('products.edit', $product)); ?>"
                           class="w-9 h-9 rounded-full bg-white flex items-center justify-center hover:scale-110 transition-transform"
                           title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4" style="color:var(--primary)"></i>
                        </a>
                        <form method="POST" action="<?php echo e(route('products.destroy', $product)); ?>" id="deleteProductCard_<?php echo e($product->id); ?>" class="hidden">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        </form>
                        <button type="button" onclick="confirmDelete('deleteProductCard_<?php echo e($product->id); ?>', 'produk <?php echo e($product->name); ?>')"
                                class="w-9 h-9 rounded-full bg-white flex items-center justify-center hover:scale-110 transition-transform"
                                title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="p-3.5 flex flex-col gap-1.5 flex-1 min-w-0">
                    <div class="min-w-0">
                        <p class="font-semibold text-sm leading-tight" style="color:var(--text-dark)"><?php echo e($product->name); ?></p>
                        <p class="text-xs" style="color:var(--text-soft)"><?php echo e($product->code); ?></p>
                    </div>

                    <div class="flex flex-wrap gap-1 mt-0.5">
                        <?php if($product->size): ?>
                        <span class="badge badge-blue-soft text-xs"><?php echo e($product->size); ?></span>
                        <?php endif; ?>
                        <?php if($product->color): ?>
                        <span class="badge badge-indigo-soft text-xs"><?php echo e($product->color); ?></span>
                        <?php endif; ?>
                        <?php if($product->category): ?>
                        <span class="badge badge-indigo-soft text-xs"><?php echo e($product->category->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-end justify-between mt-auto pt-1.5 border-t" style="border-color:var(--border)">
                        <div>
                            <p class="font-bold text-sm leading-tight" style="color:var(--text-dark)">
                                Rp <?php echo e(number_format($product->rental_price, 0, ',', '.')); ?>

                            </p>
                            <p class="text-xs" style="color:var(--text-soft)">/hari</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium leading-tight" style="color:var(--text-dark)">
                                <?php echo e($product->stock_available); ?>/<?php echo e($product->stock_total); ?>

                            </p>
                            <p class="text-xs" style="color:var(--text-soft)">stok</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div id="view-table" class="card overflow-hidden hidden">
        <div class="overflow-x-auto">
            <table class="elegant-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Produk</th>
                        <th class="text-left">Kategori</th>
                        <th class="text-center">Ukuran</th>
                        <th class="text-center">Warna</th>
                        <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <th class="text-left">Cabang</th>
                        <?php endif; ?>
                        <?php endif; ?>
                        <th class="text-center">Stok</th>
                        <th class="text-right">Harga Sewa</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Kondisi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex-shrink-0 overflow-hidden"
                                     style="background:var(--secondary)">
                                    <?php if($product->photo): ?>
                                        <img src="<?php echo e($product->photo_url); ?>"
                                             alt="<?php echo e($product->name); ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i data-lucide="shirt" class="w-5 h-5" style="color:var(--primary)"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm" style="color:var(--text-dark)"><?php echo e($product->name); ?></p>
                                    <p class="text-xs" style="color:var(--text-soft)"><?php echo e($product->code); ?></p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm" style="color:var(--text-soft)"><?php echo e($product->category?->name ?? '-'); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-gray"><?php echo e($product->size ?? '-'); ?></span>
                        </td>
                        <td class="text-center">
                            <span class="text-sm" style="color:var(--text-soft)"><?php echo e($product->color ?? '-'); ?></span>
                        </td>
                        <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                        <td class="text-left">
                            <?php $__currentLoopData = $product->branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-block rounded-full bg-blue-50 text-blue-700 text-xs px-2 py-0.5 mb-1"><?php echo e($branch->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($product->branches->isEmpty()): ?>
                                <span class="text-xs text-slate-400">-</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <?php endif; ?>
                        <td class="text-center">
                            <div class="text-sm font-medium" style="color:var(--text-dark)">
                                <?php echo e($product->stock_available); ?>/<?php echo e($product->stock_total); ?>

                            </div>
                            <div class="text-xs" style="color:var(--text-soft)">tersedia</div>
                        </td>
                        <td class="text-right">
                            <span class="font-semibold text-sm" style="color:var(--text-dark)">
                                Rp <?php echo e(number_format($product->rental_price, 0, ',', '.')); ?>

                            </span>
                            <div class="text-xs" style="color:var(--text-soft)">/hari</div>
                        </td>
                        <td class="text-center">
                            <?php if($product->status === 'available'): ?>
                                <span class="badge badge-green">Tersedia</span>
                            <?php elseif($product->status === 'rented'): ?>
                                <span class="badge badge-blue">Disewa</span>
                            <?php elseif($product->status === 'maintenance'): ?>
                                <span class="badge badge-red">Maintenance</span>
                            <?php else: ?>
                                <span class="badge badge-gray"><?php echo e($product->status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($product->condition === 'excellent'): ?>
                                <span class="badge badge-gold">Excellent</span>
                            <?php elseif($product->condition === 'good'): ?>
                                <span class="badge badge-green">Good</span>
                            <?php elseif($product->condition === 'fair'): ?>
                                <span class="badge badge-yellow">Fair</span>
                            <?php else: ?>
                                <span class="badge badge-red"><?php echo e($product->condition ?? '-'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="<?php echo e(route('products.show', $product)); ?>"
                                   class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Detail">
                                    <i data-lucide="eye" class="w-4 h-4" style="color:var(--text-soft)"></i>
                                </a>
                                <?php if(!auth()->user()->isSales()): ?>
                                <a href="<?php echo e(route('products.edit', $product)); ?>"
                                   class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4" style="color:var(--primary)"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('products.destroy', $product)); ?>" id="deleteProductTable_<?php echo e($product->id); ?>" class="hidden">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                </form>
                                <button type="button" onclick="confirmDelete('deleteProductTable_<?php echo e($product->id); ?>', 'produk <?php echo e($product->name); ?>')"
                                        class="p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-400"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($products->hasPages()): ?>
    <div class="px-6 py-4 border-t" style="border-color:var(--border)">
        <?php echo e($products->links('components.pagination')); ?>

    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function setView(view) {
    const cardView = document.getElementById('view-card');
    const tableView = document.getElementById('view-table');
    const btnCard = document.getElementById('btn-card');
    const btnTable = document.getElementById('btn-table');

    if (view === 'card') {
        cardView.classList.remove('hidden');
        tableView.classList.add('hidden');
        btnCard.style.background = 'var(--card-bg)';
        btnCard.style.color = 'var(--text-dark)';
        btnCard.style.boxShadow = '0 1px 3px rgba(0,0,0,.08)';
        btnTable.style.background = 'transparent';
        btnTable.style.color = 'var(--text-soft)';
        btnTable.style.boxShadow = 'none';
        localStorage.setItem('productView', 'card');
    } else {
        cardView.classList.add('hidden');
        tableView.classList.remove('hidden');
        btnTable.style.background = 'var(--card-bg)';
        btnTable.style.color = 'var(--text-dark)';
        btnTable.style.boxShadow = '0 1px 3px rgba(0,0,0,.08)';
        btnCard.style.background = 'transparent';
        btnCard.style.color = 'var(--text-soft)';
        btnCard.style.boxShadow = 'none';
        localStorage.setItem('productView', 'table');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('productView') || 'card';
    setView(savedView);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/products/index.blade.php ENDPATH**/ ?>