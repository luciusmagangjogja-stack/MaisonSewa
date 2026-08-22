<?php $__env->startSection('title', 'Penyewaan — SewaJas'); ?>
<?php $__env->startSection('page-title', 'Penyewaan'); ?>
<?php $__env->startSection('subtitle', 'Kelola semua transaksi penyewaan jas'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Quick Actions & Filters -->
    <div class="card-container mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <a href="<?php echo e(route('rentals.create')); ?>" class="btn-primary">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Penyewaan
                </a>
            </div>

            <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <!-- Status Filter -->
                <select name="status" class="form-input sm:w-44">
                    <option value="">Semua Status</option>
                    <option value="waiting" <?php echo e(request('status') === 'waiting' ? 'selected' : ''); ?>>Menunggu</option>
                    <option value="processing" <?php echo e(request('status') === 'processing' ? 'selected' : ''); ?>>Diproses</option>
                    <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Aktif</option>
                    <option value="overdue" <?php echo e(request('status') === 'overdue' ? 'selected' : ''); ?>>Terlambat</option>
                    <option value="returned" <?php echo e(request('status') === 'returned' ? 'selected' : ''); ?>>Selesai</option>
                </select>

                <button type="submit" class="btn-filter whitespace-nowrap">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>

                <?php if(request('search') || request('status')): ?>
                    <a href="<?php echo e(route('rentals.index')); ?>" class="btn-secondary whitespace-nowrap">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Table Container -->
    <div class="card-container p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="elegant-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">No. Invoice</th>
                        <th class="text-left">Pelanggan</th>
                        <th class="text-center">Koleksi</th>
                        <th class="text-left">Tanggal Sewa</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Total Bayar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-sand/30">
                    <?php $__empty_1 = true; $__currentLoopData = $rentals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rental): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-serif font-bold text-bark-dark text-sm leading-tight"><?php echo e($rental->invoice_number); ?></div>
                        </td>
<td class="px-6 py-4">
                            <div class="font-semibold text-bark-dark text-sm leading-tight"><?php echo e(optional($rental->customer)->name ?? '-'); ?></div>
                            <div class="text-xs text-stone-400 mt-0.5"><?php echo e(optional($rental->customer)->phone ?? '-'); ?></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="badge badge-gray text-xs">
                                <?php echo e($rental->items->count()); ?> Item
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-semibold text-bark-light leading-tight">
                                <?php echo e(optional($rental->rental_date)->format('d M Y') ?? ''); ?>

                            </div>
                            <div class="text-[10px] text-stone-400 mt-0.5">
                                Tempo: <?php echo e(optional($rental->return_due_date)->format('d M Y') ?? ''); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php
                                $statusClasses = [
                                    'waiting' => 'badge-menunggu',
                                    'processing' => 'badge-blue',
                                    'active' => 'badge-active',
                                    'overdue' => 'badge-terlambat',
                                    'returned' => 'badge-selesai',
                                    'cancelled' => 'badge-dibatalkan',
                                ];
                                $statusLabels = [
                                    'waiting' => 'Menunggu',
                                    'processing' => 'Diproses',
                                    'active' => 'Aktif',
                                    'overdue' => 'Terlambat',
                                    'returned' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                ];
                            ?>
                            <span class="badge <?php echo e($statusClasses[$rental->rental_status] ?? 'badge-gray'); ?> text-xs">
                                <?php echo e($statusLabels[$rental->rental_status] ?? $rental->rental_status); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-serif font-bold text-bark-dark text-sm">Rp<?php echo e(number_format($rental->total_amount, 0, ',', '.')); ?></div>
                        </td>
                        <td class="px-6 py-4 text-center min-w-[180px]">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="<?php echo e(route('rentals.show', $rental)); ?>" class="action-btn" title="View">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <?php if(auth()->guard()->check()): ?>
                                <?php if(auth()->user()->isSuperAdmin()): ?>
                                <a href="<?php echo e(route('rentals.edit', $rental)); ?>" class="action-btn" title="Edit">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <?php else: ?>
                                <span class="inline-block w-4 h-4"></span>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if(auth()->guard()->guest()): ?>
                                <span class="inline-block w-4 h-4"></span>
                                <?php endif; ?>
                                <a href="<?php echo e(route('rentals.receipt.show', $rental)); ?>" class="action-btn" title="Receipt">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </a>
                                <a href="<?php echo e(route('rentals.pdf', $rental)); ?>" class="action-btn" title="Download PDF">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                </a>

                                <a href="<?php echo e(route('rentals.whatsapp', $rental)); ?>" class="action-btn" title="Whatsapp">
                                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                                </a>

                                <?php if(auth()->guard()->check()): ?>
                                <?php if(auth()->user()->isSuperAdmin()): ?>
                                <form method="POST" action="<?php echo e(route('rentals.destroy', $rental)); ?>" id="deleteRentalForm_<?php echo e($rental->id); ?>" class="hidden">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                </form>
                                <button type="button" onclick="confirmDelete('deleteRentalForm_<?php echo e($rental->id); ?>', 'penyewaan <?php echo e($rental->invoice_number); ?>')" class="action-btn" style="border-color: #ef4444; color: #ef4444;" title="Hapus Penyewaan">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                                <?php else: ?>
                                <span class="inline-block w-4 h-4"></span>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if(auth()->guard()->guest()): ?>
                                <span class="inline-block w-4 h-4"></span>
                                <?php endif; ?>

                                <?php if($rental->rental_status == 'active' || $rental->rental_status == 'overdue'): ?>
                                    <a href="<?php echo e(route('rentals.show', $rental)); ?>" class="action-btn" title="Process Payment">
                                        <i data-lucide="wallet" class="w-4 h-4"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="inline-block w-4 h-4"></span>
                                <?php endif; ?>

                                <?php if($rental->rental_status == 'returned'): ?>
                                    <a href="<?php echo e(route('rentals.show', $rental)); ?>" class="action-btn" title="Payment History">
                                        <i data-lucide="history" class="w-4 h-4"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="inline-block w-4 h-4"></span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center bg-cream/10">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center text-gold mb-4 shadow-sm">
                                    <i data-lucide="shirt" class="w-8 h-8"></i>
                                </div>
                                <h3 class="font-serif text-lg font-bold text-bark-dark mb-1">Belum Ada Transaksi</h3>
                                <p class="text-xs text-stone-400 mb-6 leading-relaxed">Mulai buat transaksi penyewaan jas pertama Anda dengan mudah.</p>
                                <a href="<?php echo e(route('rentals.create')); ?>" class="btn-primary">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    Tambah Penyewaan
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($rentals->hasPages()): ?>
        <div class="px-6 py-4 border-t border-cream-sand/50 bg-cream/5">
            <?php echo e($rentals->appends(request()->query())->links('components.pagination')); ?>

        </div>
        <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/index.blade.php ENDPATH**/ ?>