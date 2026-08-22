

<?php $__env->startSection('title', 'Notifikasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Notifikasi</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Daftar notifikasi untuk akun Anda</p>
        </div>

        <form method="POST" action="<?php echo e(route('notifications.read-all')); ?>" class="inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn-secondary">
                <i data-lucide="check" class="w-4 h-4"></i> Tandai semua dibaca
            </button>
        </form>
    </div>

    <?php echo $__env->make('components.flash-messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="card p-5">
        <form method="GET" action="<?php echo e(route('notifications.index')); ?>" class="flex flex-col sm:flex-row gap-3 items-end justify-between">
            <div class="w-full sm:w-72">
                <label class="block text-sm font-medium" style="color: var(--text-soft)">Tipe</label>
                <select name="type" class="form-input mt-1">
                    <option value="all" <?php echo e($type === 'all' ? 'selected' : ''); ?>>Semua</option>
                    <option value="rental_new" <?php echo e($type === 'rental_new' ? 'selected' : ''); ?>>Penyewaan Baru</option>
                    <option value="rental_return" <?php echo e($type === 'rental_return' ? 'selected' : ''); ?>>Pengembalian</option>
                    <option value="rental_late" <?php echo e($type === 'rental_late' ? 'selected' : ''); ?>>Keterlambatan</option>
                    <option value="payment" <?php echo e($type === 'payment' ? 'selected' : ''); ?>>Pembayaran</option>
                    <option value="reminder" <?php echo e($type === 'reminder' ? 'selected' : ''); ?>>Reminder</option>
                    <option value="system" <?php echo e($type === 'system' ? 'selected' : ''); ?>>Sistem</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-primary">
                    <i data-lucide="search" class="w-4 h-4"></i> Terapkan
                </button>
                <a href="<?php echo e(route('notifications.index')); ?>" class="btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <?php if($notifications->count() === 0): ?>
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--secondary)">
                    <i data-lucide="bell" class="w-8 h-8" style="color:var(--primary)"></i>
                </div>
                <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada notifikasi</p>
                <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Coba ubah filter tipe</p>
            </div>
        <?php else: ?>
            <table class="elegant-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Notifikasi</th>
                        <th class="text-left">Waktu</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center <?php echo e($n->icon_class); ?>" style="color: white">
                                        <span class="text-lg"><?php echo e($n->icon_name); ?></span>
                                    </div>
                                    <div>
                                        <p class="font-semibold" style="color:var(--text-dark)"><?php echo e($n->title); ?></p>
                                        <p class="text-sm mt-0.5 line-clamp-2" style="color:var(--text-soft)"><?php echo e($n->message); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-sm" style="color:var(--text-dark)"><?php echo e($n->time_ago); ?></p>
                                <?php if($n->meta): ?>
                                    <p class="text-xs" style="color:var(--text-soft)"><?php echo e(is_array($n->meta) && isset($n->meta['branch_id']) ? 'Cabang #' . $n->meta['branch_id'] : ''); ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($n->is_read): ?>
                                    <span class="badge badge-green">Dibaca</span>
                                <?php else: ?>
                                    <span class="badge badge-blue">Belum dibaca</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($n->action_url): ?>
                                    <a href="<?php echo e($n->action_url); ?>" class="btn-secondary btn-sm">
                                        <i data-lucide="external-link" class="w-4 h-4"></i> Buka
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs" style="color:var(--text-soft)">-</span>
                                <?php endif; ?>
                                <a href="<?php echo e(route('notifications.show', $n->id)); ?>" class="ml-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Detail">
                                    <i data-lucide="eye" class="w-4 h-4" style="color:var(--text-soft)"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div class="px-4 py-4">
                <?php echo e($notifications->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/notifications/index.blade.php ENDPATH**/ ?>