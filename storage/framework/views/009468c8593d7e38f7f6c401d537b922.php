<?php $__env->startSection('title', 'Laporan Pengembalian'); ?>
<?php $__env->startSection('page-title', 'Laporan Pengembalian'); ?>
<?php $__env->startSection('subtitle', 'Monitoring pengembalian dan keterlambatan jas'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#10B98115">
                <i data-lucide="check-circle" class="w-6 h-6" style="color:#10B981"></i>
            </div>
            <div>
                <p class="text-xs" style="color:var(--text-soft)">Sudah Dikembalikan</p>
                <p class="text-2xl font-bold font-playfair" style="color:var(--text-dark)"><?php echo e($summary['returned']); ?></p>
                <p class="text-xs" style="color:var(--text-soft)">Periode ini</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#F59E0B15">
                <i data-lucide="clock" class="w-6 h-6" style="color:#F59E0B"></i>
            </div>
            <div>
                <p class="text-xs" style="color:var(--text-soft)">Jatuh Tempo Hari Ini</p>
                <p class="text-2xl font-bold font-playfair" style="color:var(--text-dark)"><?php echo e($summary['due_today']); ?></p>
                <p class="text-xs" style="color:var(--text-soft)">Harus segera dikembalikan</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#EF444415">
                <i data-lucide="alert-circle" class="w-6 h-6" style="color:#EF4444"></i>
            </div>
            <div>
                <p class="text-xs" style="color:var(--text-soft)">Terlambat Dikembalikan</p>
                <p class="text-2xl font-bold font-playfair text-red-500"><?php echo e($summary['overdue']); ?></p>
                <p class="text-xs" style="color:var(--text-soft)">Melewati batas waktu</p>
            </div>
        </div>
    </div>

    
    
    <?php if($overdue->count() > 0): ?>
    <div class="card overflow-hidden border-l-4" style="border-left-color:#EF4444">
        <div class="p-5 border-b flex items-center gap-2" style="border-color:var(--border)">
            <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
            <h2 class="font-semibold text-sm text-red-600">Terlambat Dikembalikan (<?php echo e($overdue->count()); ?>)</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full elegant-table">
            <thead>
                <tr>
                    <th class="text-left">Invoice</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">Tgl Kembali</th>
                    <th class="text-center">Terlambat</th>
                    <th class="text-right">Total Denda</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $overdue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="bg-red-50">
                    
                    <td class="font-mono text-xs font-semibold" style="color:var(--primary)"><?php echo e($t->invoice_number); ?></td>
                    <td>
                        
                        <p class="font-medium text-sm" style="color:var(--text-dark)"><?php echo e($t->customer?->name ?? '-'); ?></p>
                        <p class="text-xs" style="color:var(--text-soft)"><?php echo e($t->customer?->phone ?? '-'); ?></p>
                    </td>
                    
                    <td class="text-sm font-semibold text-red-500">
                        <?php echo e($t->return_due_date ? \Carbon\Carbon::parse($t->return_due_date)->format('d M Y') : '-'); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge badge-red">
                            <?php echo e($t->return_due_date ? \Carbon\Carbon::parse($t->return_due_date)->diffForHumans() : '-'); ?>

                        </span>
                    </td>
                    <td class="text-right text-sm font-semibold" style="color:var(--text-dark)">
                        <?php echo e($t->fine_amount ? 'Rp ' . number_format($t->fine_amount, 0, ',', '.') : '-'); ?>

                    </td>
                    <td class="text-center">
                        <a href="<?php echo e(route('rentals.show', $t)); ?>"
                           class="btn-primary text-xs py-1 px-3 inline-flex items-center gap-1">
                            <i data-lucide="eye" class="w-3 h-3"></i> Detail
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>

    
    
    <?php if($dueToday->count() > 0): ?>
    <div class="card overflow-hidden border-l-4" style="border-left-color:#F59E0B">
        <div class="p-5 border-b flex items-center gap-2" style="border-color:var(--border)">
            <i data-lucide="clock" class="w-4 h-4" style="color:#F59E0B"></i>
            <h2 class="font-semibold text-sm" style="color:#92400E">Jatuh Tempo Hari Ini (<?php echo e($dueToday->count()); ?>)</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full elegant-table">
            <thead>
                <tr>
                    <th class="text-left">Invoice</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">No. HP</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $dueToday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="background:#FFFBEB">
                    <td class="font-mono text-xs font-semibold" style="color:var(--primary)"><?php echo e($t->invoice_number); ?></td>
                    <td class="font-medium text-sm" style="color:var(--text-dark)"><?php echo e($t->customer?->name ?? '-'); ?></td>
                    <td>
                        
                        <?php if($t->customer?->phone): ?>
                        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $t->customer->phone)); ?>"
                           target="_blank"
                           class="flex items-center gap-1 text-sm text-green-600 hover:text-green-700">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            <?php echo e($t->customer->phone); ?>

                        </a>
                        <?php else: ?>
                        <span class="text-sm" style="color:var(--text-soft)">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo e(route('rentals.show', $t)); ?>"
                           class="btn-secondary text-xs py-1 px-3 inline-flex items-center gap-1">
                            <i data-lucide="eye" class="w-3 h-3"></i> Detail
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="card p-5">
        <form method="GET" action="<?php echo e(route('reports.returns')); ?>" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium mb-1" style="color:var(--text-soft)">Dari Tanggal</label>
                <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" class="form-input">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color:var(--text-soft)">Sampai Tanggal</label>
                <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" class="form-input">
            </div>
            <button type="submit" class="btn-primary">
                <i data-lucide="search" class="w-4 h-4"></i> Tampilkan
            </button>
            <a href="<?php echo e(route('reports.returns')); ?>" class="btn-secondary">Reset</a>
        </form>
    </div>

    
    
    <div class="card overflow-hidden">
        <div class="p-5 border-b" style="border-color:var(--border)">
            <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Riwayat Pengembalian</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full elegant-table">
            <thead>
                <tr>
                    <th class="text-left">Invoice</th>
                    <th class="text-left">Customer</th>
                    <th class="text-left">Tgl Sewa</th>
                    <th class="text-left">Tgl Dikembalikan</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $returned; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-mono text-xs font-semibold" style="color:var(--primary)"><?php echo e($t->invoice_number); ?></td>
                    <td>
                        <p class="font-medium text-sm" style="color:var(--text-dark)"><?php echo e($t->customer?->name ?? '-'); ?></p>
                        <p class="text-xs" style="color:var(--text-soft)"><?php echo e($t->customer?->phone ?? '-'); ?></p>
                    </td>
                    
                    <td class="text-sm" style="color:var(--text-soft)"><?php echo e($t->created_at->format('d M Y')); ?></td>
                    
                    <td class="text-sm" style="color:var(--text-soft)">
                        <?php echo e($t->actual_return_date ? \Carbon\Carbon::parse($t->actual_return_date)->format('d M Y') : '-'); ?>

                    </td>
                    
                    <td class="text-right font-semibold text-sm" style="color:var(--text-dark)">
                        Rp <?php echo e(number_format($t->total_amount, 0, ',', '.')); ?>

                    </td>
                    
                    <td class="text-center">
                        <span class="badge <?php echo e($t->rental_status === 'completed' ? 'badge-green' : 'badge-blue'); ?>">
                            <?php echo e(ucfirst($t->rental_status)); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2" style="color:var(--border)"></i>
                        <p class="text-sm" style="color:var(--text-soft)">Belum ada data pengembalian</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php if($returned->hasPages()): ?>
        <div class="px-6 py-4 border-t" style="border-color:var(--border)">
            <?php echo e($returned->links('components.pagination')); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/reports/returns.blade.php ENDPATH**/ ?>