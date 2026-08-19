<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="Pagination Navigation" class="ds-pagination flex flex-col sm:flex-row justify-between items-center gap-4">
        
        <div class="flex justify-between w-full sm:hidden">
            <?php if($paginator->onFirstPage()): ?>
                <span class="px-4 py-2 text-sm font-bold text-slate-400 bg-white border border-slate-200 rounded-2xl cursor-default">
                    <?php echo e(__('pagination.previous')); ?>

                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="px-4 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl ds-transition hover:bg-blue-50 hover:border-blue-400 hover:-translate-y-0.5">
                    <?php echo e(__('pagination.previous')); ?>

                </a>
            <?php endif; ?>

            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="px-4 py-2 ml-3 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl ds-transition hover:bg-blue-50 hover:border-blue-400 hover:-translate-y-0.5">
                    <?php echo e(__('pagination.next')); ?>

                </a>
            <?php else: ?>
                <span class="px-4 py-2 ml-3 text-sm font-bold text-slate-400 bg-white border border-slate-200 rounded-2xl cursor-default">
                    <?php echo e(__('pagination.next')); ?>

                </span>
            <?php endif; ?>
        </div>

        
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between w-full">
            <div>
                <p class="text-sm font-semibold text-slate-600">
                    <?php echo e(__('Showing')); ?>

                    <?php if($paginator->firstItem()): ?>
                        <span class="font-bold text-slate-900"><?php echo e($paginator->firstItem()); ?></span>
                        <?php echo e(__('to')); ?>

                        <span class="font-bold text-slate-900"><?php echo e($paginator->lastItem()); ?></span>
                    <?php else: ?>
                        <?php echo e($paginator->count()); ?>

                    <?php endif; ?>
                    <?php echo e(__('of')); ?>

                    <span class="font-bold text-slate-900"><?php echo e($paginator->total()); ?></span>
                    <?php echo e(__('results')); ?>

                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex items-center gap-1 rounded-2xl">
                    
                    <?php if($paginator->onFirstPage()): ?>
                        <span aria-disabled="true" aria-label="<?php echo e(__('pagination.previous')); ?>">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-slate-400 bg-white border border-slate-200 rounded-2xl cursor-default" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    <?php else: ?>
                        <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-2xl ds-transition hover:bg-blue-50 hover:border-blue-400 hover:-translate-y-0.5" aria-label="<?php echo e(__('pagination.previous')); ?>">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    <?php endif; ?>

                    
                    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_string($element)): ?>
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-slate-400 bg-white border border-slate-200 rounded-2xl cursor-default"><?php echo e($element); ?></span>
                            </span>
                        <?php endif; ?>

                        <?php if(is_array($element)): ?>
                            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page == $paginator->currentPage()): ?>
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-gradient-to-br from-blue-600 to-blue-700 border border-blue-500 rounded-2xl shadow-md shadow-blue-200 cursor-default"><?php echo e($page); ?></span>
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo e($url); ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl ds-transition hover:bg-blue-50 hover:border-blue-400 hover:-translate-y-0.5" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>">
                                        <?php echo e($page); ?>

                                    </a>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    
                    <?php if($paginator->hasMorePages()): ?>
                        <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-2xl ds-transition hover:bg-blue-50 hover:border-blue-400 hover:-translate-y-0.5" aria-label="<?php echo e(__('pagination.next')); ?>">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 1.414z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    <?php else: ?>
                        <span aria-disabled="true" aria-label="<?php echo e(__('pagination.next')); ?>">
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-bold text-slate-400 bg-white border border-slate-200 rounded-2xl cursor-default" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
    </nav>
<?php endif; ?>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/components/pagination.blade.php ENDPATH**/ ?>