<?php $__env->startSection('title', 'Detail Produk — ' . $product->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">

        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('products.index')); ?>" class="btn-secondary p-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)"><?php echo e($product->name); ?></h1>
                    <p class="text-sm mt-0.5" style="color: var(--text-soft)"><?php echo e($product->code); ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <?php if(!auth()->user()->isSales()): ?>
                <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn-primary">
                    <i data-lucide="pencil" class="w-4 h-4"></i> Edit Produk
                </a>
                <form method="POST" action="<?php echo e(route('products.destroy', $product)); ?>" id="deleteProductShow_<?php echo e($product->id); ?>" class="hidden">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                </form>
                <button type="button" onclick="confirmDelete('deleteProductShow_<?php echo e($product->id); ?>', 'produk <?php echo e($product->name); ?>')" class="btn-secondary text-red-500 hover:bg-red-50">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Hapus</span>
                </button>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            
            <div class="lg:col-span-2 space-y-6">

                
                <div class="card overflow-hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2">
                        
                        <div class="relative bg-gray-50 flex items-center justify-center min-h-56"
                            style="background: var(--secondary)">
                            <?php if($product->photo): ?>
                                <img src="<?php echo e($product->photo_url); ?>" alt="<?php echo e($product->name); ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="flex flex-col items-center gap-3 p-8 text-center">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center"
                                        style="background: rgba(var(--primary-rgb), 0.1)">
                                        <i data-lucide="shirt" class="w-8 h-8" style="color:var(--primary)"></i>
                                    </div>
                                    <p class="text-xs" style="color:var(--text-soft)">Belum ada foto</p>
                                </div>
                            <?php endif; ?>

                            
                            <div class="absolute top-3 left-3">
                                <?php if($product->status === 'available'): ?>
                                    <span class="badge badge-green">Tersedia</span>
                                <?php elseif($product->status === 'rented'): ?>
                                    <span class="badge badge-blue">Disewa</span>
                                <?php elseif($product->status === 'maintenance'): ?>
                                    <span class="badge badge-red">Maintenance</span>
                                <?php else: ?>
                                    <span class="badge badge-gray"><?php echo e($product->status); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="p-6 flex flex-col justify-between">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-medium uppercase tracking-wide mb-0.5"
                                        style="color:var(--text-soft)">Harga Sewa</p>
                                    <p class="text-3xl font-bold font-playfair" style="color:var(--text-dark)">
                                        Rp <?php echo e(number_format($product->rental_price, 0, ',', '.')); ?>

                                    </p>
                                    <p class="text-xs" style="color:var(--text-soft)">per hari</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-xl p-3" style="background:var(--secondary)">
                                        <p class="text-xs" style="color:var(--text-soft)">Ukuran</p>
                                        <p class="font-semibold text-sm mt-0.5" style="color:var(--text-dark)">
                                            <?php echo e($product->size ?? '—'); ?></p>
                                    </div>
                                    <div class="rounded-xl p-3" style="background:var(--secondary)">
                                        <p class="text-xs" style="color:var(--text-soft)">Warna</p>
                                        <p class="font-semibold text-sm mt-0.5" style="color:var(--text-dark)">
                                            <?php echo e($product->color ?? '—'); ?></p>
                                    </div>
                                    <div class="rounded-xl p-3" style="background:var(--secondary)">
                                        <p class="text-xs" style="color:var(--text-soft)">Stok</p>
                                        <p class="font-semibold text-sm mt-0.5" style="color:var(--text-dark)">
                                            <?php echo e($product->stock_available); ?>/<?php echo e($product->stock_total); ?>

                                        </p>
                                    </div>
                                    <div class="rounded-xl p-3" style="background:var(--secondary)">
                                        <p class="text-xs" style="color:var(--text-soft)">Kondisi</p>
                                        <div class="mt-0.5">
                                            <?php if($product->condition === 'excellent'): ?>
                                                <span class="badge badge-gold text-xs">Excellent</span>
                                            <?php elseif($product->condition === 'good'): ?>
                                                <span class="badge badge-green text-xs">Good</span>
                                            <?php elseif($product->condition === 'fair'): ?>
                                                <span class="badge badge-yellow text-xs">Fair</span>
                                            <?php else: ?>
                                                <span class="badge badge-red text-xs">Poor</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-xs" style="color:var(--text-soft)">Ketersediaan stok</p>
                                    <p class="text-xs font-medium" style="color:var(--text-dark)">
                                        <?php echo e($product->stock_total > 0 ? round(($product->stock_available / $product->stock_total) * 100) : 0); ?>%
                                    </p>
                                </div>
                                <div class="w-full h-1.5 rounded-full overflow-hidden" style="background:var(--border)">
                                    <?php
                                        $pct =
                                            $product->stock_total > 0
                                                ? round(($product->stock_available / $product->stock_total) * 100)
                                                : 0;
                                        $barColor = $pct > 50 ? '#22c55e' : ($pct > 20 ? '#f59e0b' : '#ef4444');
                                    ?>
                                    <div class="h-full rounded-full transition-all"
                                        style="width:<?php echo e($pct); ?>%; background:<?php echo e($barColor); ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <?php if($product->description): ?>
                    <div class="card p-6">
                        <div class="flex items-center gap-2 pb-3 mb-4 border-b" style="border-color:var(--border)">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                style="background:var(--secondary)">
                                <i data-lucide="align-left" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                            </div>
                            <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Deskripsi</h2>
                        </div>
                        <p class="text-sm leading-relaxed" style="color:var(--text-soft)"><?php echo e($product->description); ?></p>
                    </div>
                <?php endif; ?>

                
                <div class="card overflow-hidden">
                    <div class="p-6 pb-4 flex items-center justify-between border-b" style="border-color:var(--border)">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                style="background:var(--secondary)">
                                <i data-lucide="history" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                            </div>
                            <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Riwayat Rental</h2>
                        </div>
                        <?php $rentalItemsList = $product->rentalItems ?? collect(); ?>
                        <?php if($rentalItemsList->isNotEmpty()): ?>
                            <span class="badge badge-gray"><?php echo e($rentalItemsList->count()); ?> transaksi</span>
                        <?php endif; ?>
                    </div>

                    <?php if($rentalItemsList->isNotEmpty()): ?>
                        <div class="overflow-x-auto">
                            <table class="elegant-table w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left">Pelanggan</th>
                                        <th class="text-center">Tanggal Sewa</th>
                                        <th class="text-center">Tanggal Kembali</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $rentalItemsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $r = $ri->rental; ?>
                                        <?php if($r): ?>
                                        <tr>
                                            <td>
                                                <p class="text-sm font-medium" style="color:var(--text-dark)">
                                                    <?php echo e(optional($r->customer)->name ?? '-'); ?></p>
                                                <p class="text-xs" style="color:var(--text-soft)">
                                                    <?php echo e(optional($r->customer)->phone ?? ''); ?></p>
                                            </td>
                                            <td class="text-center text-sm" style="color:var(--text-soft)">
                                                <?php echo e(optional($r->rental_date)->format('d M Y') ?? '-'); ?>

                                            </td>
                                            <td class="text-center text-sm" style="color:var(--text-soft)">
                                                <?php echo e(optional($r->return_due_date)->format('d M Y') ?? '-'); ?>

                                            </td>
                                            <td class="text-right text-sm font-semibold" style="color:var(--text-dark)">
                                                Rp <?php echo e(number_format($r->total_amount ?? 0, 0, ',', '.')); ?>

                                            </td>
                                            <td class="text-center">
                                                <?php if($r->rental_status === 'active'): ?>
                                                    <span class="badge badge-blue">Aktif</span>
                                                <?php elseif($r->rental_status === 'returned'): ?>
                                                    <span class="badge badge-green">Dikembalikan</span>
                                                <?php elseif($r->rental_status === 'overdue'): ?>
                                                    <span class="badge badge-red">Terlambat</span>
                                                <?php else: ?>
                                                    <span class="badge badge-gray"><?php echo e($r->rental_status); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3"
                                style="background:var(--secondary)">
                                <i data-lucide="calendar-x" class="w-5 h-5" style="color:var(--text-soft)"></i>
                            </div>
                            <p class="text-sm font-medium" style="color:var(--text-dark)">Belum ada riwayat rental</p>
                            <p class="text-xs mt-1" style="color:var(--text-soft)">Produk ini belum pernah disewa</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            
            <div class="space-y-6">

                
                <div class="card p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                            style="background:var(--secondary)">
                            <i data-lucide="list" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Detail Produk</h2>
                    </div>

                    <?php
                        $details = [
                            ['label' => 'Kode Produk', 'value' => $product->code, 'icon' => 'tag'],
                            ['label' => 'Kategori', 'value' => $product->category?->name ?? '—', 'icon' => 'folder'],
                            ['label' => 'Ukuran', 'value' => $product->size ?? '—', 'icon' => 'ruler'],
                            ['label' => 'Warna', 'value' => $product->color ?? '—', 'icon' => 'palette'],
                            ['label' => 'Stok Total', 'value' => $product->stock_total, 'icon' => 'layers'],
                            [
                                'label' => 'Stok Tersedia',
                                'value' => $product->stock_available,
                                'icon' => 'check-circle',
                            ],
                        ];
                    ?>

                    <div class="space-y-3">
                        <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="<?php echo e($d['icon']); ?>" class="w-3.5 h-3.5 flex-shrink-0"
                                        style="color:var(--text-soft)"></i>
                                    <span class="text-sm" style="color:var(--text-soft)"><?php echo e($d['label']); ?></span>
                                </div>
                                <span class="text-sm font-medium"
                                    style="color:var(--text-dark)"><?php echo e($d['value']); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <?php if($product->notes): ?>
                    <div class="card p-6">
                        <div class="flex items-center gap-2 pb-3 mb-3 border-b" style="border-color:var(--border)">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                style="background:var(--secondary)">
                                <i data-lucide="file-text" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                            </div>
                            <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Catatan Internal</h2>
                        </div>
                        <p class="text-sm leading-relaxed" style="color:var(--text-soft)"><?php echo e($product->notes); ?></p>
                    </div>
                <?php endif; ?>

                
                <div class="card p-5 space-y-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5" style="color:var(--text-soft)"></i>
                        <div>
                            <p class="text-xs" style="color:var(--text-soft)">Diperbarui</p>
                            <p class="text-sm font-medium" style="color:var(--text-dark)">
                                <?php echo e($product->updated_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar-plus" class="w-3.5 h-3.5" style="color:var(--text-soft)"></i>
                        <div>
                            <p class="text-xs" style="color:var(--text-soft)">Ditambahkan</p>
                            <p class="text-sm font-medium" style="color:var(--text-dark)">
                                <?php echo e($product->created_at->format('d M Y, H:i')); ?></p>
                        </div>
                    </div>
                </div>

                
                <div class="card p-5 space-y-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wide" style="color:var(--text-soft)">Aksi Cepat
                    </h3>
                    <?php if(!auth()->user()->isSales()): ?>
                    <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn-primary w-full justify-center">
                        <i data-lucide="pencil" class="w-4 h-4"></i> Edit Produk
                    </a>
                    <?php endif; ?>
                    
                    <?php if($product->status !== 'maintenance'): ?>
                        <?php if(!auth()->user()->isSales()): ?>
                        <form method="POST" action="<?php echo e(route('products.update', $product)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="maintenance">
                            <input type="hidden" name="name" value="<?php echo e($product->name); ?>">
                            <input type="hidden" name="code" value="<?php echo e($product->code); ?>">
                            <input type="hidden" name="rental_price" value="<?php echo e($product->rental_price); ?>">
                            <input type="hidden" name="stock_total" value="<?php echo e($product->stock_total); ?>">
                            <input type="hidden" name="stock_available" value="<?php echo e($product->stock_available); ?>">
                            <button type="submit"
                                class="btn-secondary w-full justify-center text-orange-500 hover:bg-orange-50">
                                <i data-lucide="wrench" class="w-4 h-4"></i> Tandai Maintenance
                            </button>
                        </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($product->status !== 'available'): ?>
                        <?php if(!auth()->user()->isSales()): ?>
                        <form method="POST" action="<?php echo e(route('products.update', $product)); ?>">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="status" value="available">
                            <input type="hidden" name="name" value="<?php echo e($product->name); ?>">
                            <input type="hidden" name="code" value="<?php echo e($product->code); ?>">
                            <input type="hidden" name="rental_price" value="<?php echo e($product->rental_price); ?>">
                            <input type="hidden" name="stock_total" value="<?php echo e($product->stock_total); ?>">
                            <input type="hidden" name="stock_available" value="<?php echo e($product->stock_available); ?>">
                            <button type="submit"
                                class="btn-secondary w-full justify-center text-green-600 hover:bg-green-50">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Tandai Tersedia
                            </button>
                        </form>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(!auth()->user()->isSales()): ?>
                    <form method="POST" action="<?php echo e(route('products.destroy', $product)); ?>" id="deleteProductQuick_<?php echo e($product->id); ?>" class="hidden">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    </form>
                    <button type="button" onclick="confirmDelete('deleteProductQuick_<?php echo e($product->id); ?>', 'produk <?php echo e($product->name); ?>')" class="btn-secondary w-full justify-center text-red-500 hover:bg-red-50">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Produk
                    </button>
                    <?php endif; ?>

                    <?php if(auth()->user()->isSales()): ?>
                    <button type="button" onclick="openStockModal()" class="btn-primary w-full justify-center">
                        <i data-lucide="package" class="w-4 h-4"></i> Update Stok
                    </button>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    
    <?php if(auth()->user()->isSales()): ?>
    <div id="stock-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold" style="color: var(--text-dark)">Update Stok Produk</h3>
                <button type="button" onclick="closeStockModal()" class="rounded-lg p-1 hover:bg-slate-100">
                    <i data-lucide="x" class="w-5 h-5" style="color: var(--text-soft)"></i>
                </button>
            </div>

            <div class="mb-4 p-4 rounded-xl bg-slate-50">
                <p class="text-sm font-semibold" style="color: var(--text-dark)"><?php echo e($product->name); ?></p>
                <p class="text-xs mt-1" style="color: var(--text-soft)">Stok Total: <?php echo e($product->stock_total); ?> | Stok Tersedia Saat Ini: <?php echo e($product->stock_available); ?></p>
            </div>

            <form method="POST" action="<?php echo e(route('products.update-stock', $product)); ?>" id="stock-update-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">
                            Stok Tersedia Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_available" id="stock_available_input"
                               value="<?php echo e($product->stock_available); ?>"
                               min="0" max="<?php echo e($product->stock_total); ?>"
                               class="form-input <?php $__errorArgs = ['stock_available'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required>
                        <p class="text-xs mt-1" style="color: var(--text-soft)">Maksimal: <?php echo e($product->stock_total); ?></p>
                        <?php $__errorArgs = ['stock_available'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">Catatan Perubahan (opsional)</label>
                        <textarea name="stock_note" rows="2" class="form-input resize-none text-sm" placeholder="Contoh: Stok bertambah karena retur..."></textarea>
                        <?php $__errorArgs = ['stock_note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="closeStockModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStockModal() {
            const modal = document.getElementById('stock-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeStockModal() {
            const modal = document.getElementById('stock-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('stock-update-form')?.addEventListener('submit', function(e) {
            const input = document.getElementById('stock_available_input');
            const max = parseInt(input.getAttribute('max'), 10);
            if (parseInt(input.value, 10) > max) {
                e.preventDefault();
                alert('Stok tersedia tidak boleh melebihi stok total (' + max + ')');
                input.focus();
            }
        });
    </script>
    <?php endif; ?>

    <?php $__env->startPush('scripts'); ?>
        <script>
            lucide.createIcons();
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/products/show.blade.php ENDPATH**/ ?>