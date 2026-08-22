<?php $__env->startSection('title', 'Laporan Pendapatan'); ?>

<?php $__env->startSection('content'); ?>


<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="font-playfair text-2xl font-semibold" style="color: var(--text-dark)">
            Laporan Pendapatan
        </h1>
        <p class="text-sm mt-0.5" style="color: var(--text-soft)">
            <?php if($isSuperAdmin): ?>
                <?php if($selectedBranchId): ?>
                    Cabang: <span class="font-semibold" style="color: var(--text-dark)">
                        <?php echo e($branches->firstWhere('id', $selectedBranchId)?->name); ?>

                    </span>
                <?php else: ?>
                    Menampilkan data <span class="font-semibold" style="color: var(--text-dark)">semua cabang</span>
                <?php endif; ?>
            <?php else: ?>
                Cabang: <span class="font-semibold" style="color: var(--text-dark)">
                    <?php echo e(auth()->user()->branch?->name ?? '-'); ?>

                </span>
            <?php endif; ?>
        </p>
    </div>
</div>


<?php echo $__env->make('reports.partials.filter-bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    
    <div class="stat-card">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                    Total Pendapatan
                </p>
                <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                    Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?>

                </p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: var(--gold-light)">
                <i data-lucide="trending-up" class="w-5 h-5" style="color: var(--gold)"></i>
            </div>
        </div>
    </div>

    
    <div class="stat-card">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                    Total Denda Keterlambatan
                </p>
                <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                    Rp <?php echo e(number_format($totalLateFee ?? 0, 0, ',', '.')); ?>

                </p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: #FFF1F0">
                <i data-lucide="alert-triangle" class="w-5 h-5" style="color: #C0392B"></i>
            </div>
        </div>
    </div>

    
    <div class="stat-card">
        <div class="flex items-start justify-between relative z-10">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                    Total Hari Transaksi
                </p>
                <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                    <?php echo e($revenueData->count()); ?>

                    <span class="text-base font-normal" style="color: var(--text-soft)">hari</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: #EFF6FF">
                <i data-lucide="calendar-days" class="w-5 h-5" style="color: #1D4ED8"></i>
            </div>
        </div>
    </div>

</div>


<div class="card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between"
         style="border-bottom: 1px solid var(--border)">
        <h2 class="font-semibold text-sm" style="color: var(--text-dark)">
            Rincian Pendapatan per Hari
        </h2>
        <span class="text-xs" style="color: var(--text-soft)">
            <?php echo e($revenueData->count()); ?> entri
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="elegant-table w-full">
            <thead>
                <tr>
                    <th class="text-left">Tanggal</th>
                    <th class="text-right">Total Rental</th>
                    <th class="text-right">Denda</th>
                    <th class="text-right">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $revenueData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td style="color: var(--text-dark)">
                            <?php echo e(\Carbon\Carbon::parse($row->date)->isoFormat('dddd, D MMMM Y')); ?>

                        </td>
                        <td class="text-right" style="color: var(--text-soft)">
                            <?php echo e(number_format($row->total_rentals)); ?>

                        </td>
                        <td class="text-right">
                            <?php if($row->total_late_fee > 0): ?>
                                <span style="color: #C0392B" class="font-medium">
                                    Rp <?php echo e(number_format($row->total_late_fee, 0, ',', '.')); ?>

                                </span>
                            <?php else: ?>
                                <span style="color: var(--text-soft)">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right font-semibold" style="color: #15803D">
                            Rp <?php echo e(number_format($row->total_revenue, 0, ',', '.')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center py-12">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-8 h-8 opacity-30"
                                   style="color: var(--text-soft)"></i>
                                <p class="text-sm" style="color: var(--text-soft)">
                                    Tidak ada data untuk periode ini
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

            <?php if($revenueData->isNotEmpty()): ?>
                <tfoot>
                    <tr style="background: linear-gradient(135deg, #F8F5F0, #EDE7DE);
                                border-top: 2px solid var(--border)">
                        <td class="font-bold text-sm px-4 py-3" style="color: var(--text-dark)">
                            Total Keseluruhan
                        </td>
                        <td class="text-right font-bold text-sm px-4 py-3" style="color: var(--text-dark)">
                            <?php echo e(number_format($revenueData->sum('total_rentals'))); ?>

                        </td>
                        <td class="text-right font-bold text-sm px-4 py-3" style="color: #C0392B">
                            Rp <?php echo e(number_format($totalLateFee ?? 0, 0, ',', '.')); ?>

                        </td>
                        <td class="text-right font-bold text-sm px-4 py-3" style="color: #15803D">
                            Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?>

                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/reports/revenue.blade.php ENDPATH**/ ?>