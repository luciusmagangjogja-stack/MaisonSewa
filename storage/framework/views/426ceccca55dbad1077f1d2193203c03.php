

<div class="card mb-6 overflow-hidden">
    <form method="GET" action="<?php echo e(url()->current()); ?>">

        
        <div class="flex flex-wrap items-end gap-4 p-5">

            
            <?php if($isSuperAdmin): ?>
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="text-xs font-semibold uppercase tracking-wider"
                           style="color: var(--text-soft)">
                        Cabang
                    </label>
                    <div class="relative">
                        <select name="branch_id" class="form-input pr-8 appearance-none cursor-pointer">
                            <option value="">— Semua Cabang —</option>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option
                                    value="<?php echo e($branch->id); ?>"
                                    <?php echo e((int) $selectedBranchId === $branch->id ? 'selected' : ''); ?>

                                >
                                    <?php echo e($branch->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <i data-lucide="chevron-down"
                           class="w-3.5 h-3.5 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"
                           style="color: var(--text-soft)"></i>
                    </div>
                </div>
            <?php else: ?>
                
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="text-xs font-semibold uppercase tracking-wider"
                           style="color: var(--text-soft)">
                        Cabang
                    </label>
                    <div class="form-input flex items-center gap-2 opacity-60 cursor-not-allowed"
                         style="background: var(--secondary)">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 flex-shrink-0"
                           style="color: var(--text-soft)"></i>
                        <span class="text-sm truncate" style="color: var(--text-dark)">
                            <?php echo e(auth()->user()->branch?->name ?? '-'); ?>

                        </span>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="flex flex-col gap-1.5">
                <label for="start_date"
                       class="text-xs font-semibold uppercase tracking-wider"
                       style="color: var(--text-soft)">
                    Dari
                </label>
                <input
                    type="date"
                    name="start_date"
                    id="start_date"
                    class="form-input"
                    value="<?php echo e(request('start_date')); ?>"
                >
            </div>

            
            <div class="flex flex-col gap-1.5">
                <label for="end_date"
                       class="text-xs font-semibold uppercase tracking-wider"
                       style="color: var(--text-soft)">
                    Sampai
                </label>
                <input
                    type="date"
                    name="end_date"
                    id="end_date"
                    class="form-input"
                    value="<?php echo e(request('end_date')); ?>"
                >
            </div>

            
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-semibold uppercase tracking-wider opacity-0">—</label>
                <button type="submit" class="btn-primary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Tampilkan
                </button>
            </div>

            
            <?php if(request()->hasAny(['start_date', 'end_date', 'branch_id'])): ?>
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider opacity-0">—</label>
                    <a href="<?php echo e(url()->current()); ?>" class="btn-secondary">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Reset
                    </a>
                </div>
            <?php endif; ?>

        </div>

        
        <div class="flex items-center justify-between px-5 py-3"
             style="background: var(--secondary); border-top: 1px solid var(--border)">

            
            <div class="flex items-center gap-2 text-xs" style="color: var(--text-soft)">
                <?php if($isSuperAdmin): ?>
                    <?php if($selectedBranchId): ?>
                        <i data-lucide="building-2" class="w-3.5 h-3.5"></i>
                        Export untuk cabang
                        <span class="font-semibold" style="color: var(--text-dark)">
                            <?php echo e($branches->firstWhere('id', $selectedBranchId)?->name); ?>

                        </span>
                    <?php else: ?>
                        <i data-lucide="globe" class="w-3.5 h-3.5" style="color: var(--gold)"></i>
                        Export akan mencakup
                        <span class="font-semibold" style="color: var(--text-dark)">semua cabang</span>
                    <?php endif; ?>
                <?php else: ?>
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                    Export hanya untuk cabang Anda
                <?php endif; ?>
            </div>

            
            <div class="flex items-center gap-2">
                
                <a href="<?php echo e(route('reports.export.excel', request()->query())); ?>"
                   class="btn-secondary text-sm py-1.5 px-3"
                   style="color: #15803D; border-color: #BBF7D0; background: #F0FDF4">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    Excel
                    <?php if($isSuperAdmin && ! $selectedBranchId): ?>
                        <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold"
                              style="background: var(--gold-light); color: #92681A">
                            Semua
                        </span>
                    <?php endif; ?>
                </a>

                
                <a href="<?php echo e(route('reports.export.pdf', request()->query())); ?>"
                   class="btn-secondary text-sm py-1.5 px-3"
                   style="color: #C0392B; border-color: #FECACA; background: #FFF1F0">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    PDF
                    <?php if($isSuperAdmin && ! $selectedBranchId): ?>
                        <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold"
                              style="background: var(--gold-light); color: #92681A">
                            Semua
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

    </form>
</div><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/reports/partials/filter-bar.blade.php ENDPATH**/ ?>