<?php $__env->startSection('title', 'Pelanggan — RentalJas'); ?>
<?php $__env->startSection('page-title', 'Pelanggan'); ?>
<?php $__env->startSection('subtitle', 'Kelola semua data pelanggan'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Deactivated Count Alert (Super Admin only) — placed OUTSIDE cards for prominence -->
    <?php if(auth()->guard()->check()): ?>
    <?php if(auth()->user()->isSuperAdmin() && isset($deactivatedCount) && $deactivatedCount > 0): ?>
    <div class="ds-card mb-6 px-5 py-4 bg-amber-50/80 border border-amber-200 flex items-center gap-3 ds-slide-up">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-100">
            <i data-lucide="info" class="w-5 h-5 text-amber-600"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-amber-900">
                <strong><?php echo e($deactivatedCount); ?></strong> pelanggan dalam status dinonaktifkan.
            </p>
        </div>
        <a href="<?php echo e(route('customers.index', ['status' => 'deactivated'])); ?>" class="btn-secondary whitespace-nowrap text-amber-700 border-amber-300 hover:bg-amber-100">
            <i data-lucide="eye" class="w-4 h-4"></i>
            Lihat
        </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Quick Actions & Filters -->
    <div class="ds-card p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <?php if(auth()->guard()->check()): ?>
                <?php if(in_array(auth()->user()->role, ['super_admin','admin_toko','sales'])): ?>
                <a href="<?php echo e(route('customers.create')); ?>" class="btn-primary">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Pelanggan
                </a>
                <?php endif; ?>
                <?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                <?php if(in_array(auth()->user()->role, ['super_admin','admin_toko'])): ?>
                <a href="<?php echo e(route('customers.export')); ?>" class="btn-secondary">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export Excel
                </a>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto" x-data="{}">
                <div class="relative flex-1 sm:w-80">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
                    <input type="text" placeholder="Cari nama atau nomor HP..."
                           class="form-input" style="padding-left: 44px !important"
                           id="search-customer"
                           value="<?php echo e(request('search')); ?>"
                           onkeyup="if(event.key === 'Enter') applyCustomerFilters()">
                </div>

                <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->isSuperAdmin()): ?>
                <select class="form-input sm:w-44" id="status-filter" onchange="applyCustomerFilters()">
                    <option value="active" <?php echo e(($statusFilter ?? request('status', 'active')) === 'active' ? 'selected' : ''); ?>>Aktif</option>
                    <option value="deactivated" <?php echo e(($statusFilter ?? request('status')) === 'deactivated' ? 'selected' : ''); ?>>Dinonaktifkan</option>
                    <option value="all" <?php echo e(($statusFilter ?? request('status')) === 'all' ? 'selected' : ''); ?>>Semua</option>
                </select>
                <?php endif; ?>
                <?php endif; ?>

                <select class="form-input sm:w-40" id="bl-filter" onchange="applyCustomerFilters()">
                    <option value="">Blacklist: Semua</option>
                    <option value="normal" <?php echo e(request('bl_status') === 'normal' ? 'selected' : ''); ?>>Normal</option>
                    <option value="blacklisted" <?php echo e(request('bl_status') === 'blacklisted' ? 'selected' : ''); ?>>Blacklist</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="ds-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="elegant-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Nama</th>
                            <th class="text-left">Nomor Handphone</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-center">Tanggal Bergabung</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-sand/30">
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isDeactivated = $customer->trashed();
                        ?>
                        <tr class="hover:bg-cream/20 transition-colors <?php echo e($isDeactivated ? 'opacity-70 bg-slate-50' : ''); ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0 <?php echo e($isDeactivated ? 'grayscale' : ''); ?>"
                                         style="background-image: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; outline: 3px solid #fff; outline-offset: 0; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.22), 0 3px 8px rgba(99, 102, 241, 0.12);">
                                        <?php echo e(strtoupper(substr($customer->name, 0, 2))); ?>

                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-bark-dark truncate text-sm"><?php echo e($customer->name); ?></div>
                                        <?php if($isDeactivated): ?>
                                        <span class="text-xs text-amber-600 font-medium">Dinonaktifkan</span>
                                        <?php endif; ?>
                                    </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-bark-light"><?php echo e($customer->phone); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($isDeactivated): ?>
                                <span class="badge bg-amber-100 text-amber-700 border border-amber-200">Dinonaktifkan</span>
                                <?php elseif($customer->is_blacklisted): ?>
                                <span class="badge badge-red">Blacklist</span>
                                <?php else: ?>
                                <span class="badge badge-green">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-bold text-bark-dark"><?php echo e($customer->rentals_count ?? 0); ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-bark-light"><?php echo e($customer->created_at ? $customer->created_at->format('d M Y') : '-'); ?></div>
                                <?php if($isDeactivated && $customer->deleted_at): ?>
                                <div class="text-xs text-amber-500 mt-1">Nonaktif: <?php echo e($customer->deleted_at->format('d M Y')); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <?php if($isDeactivated): ?>
                                        <?php if(auth()->guard()->check()): ?>
                                        <?php if(auth()->user()->isSuperAdmin()): ?>
                                        <form method="POST" action="<?php echo e(route('customers.restore', $customer->id)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="p-2 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-xl transition-all" title="Pulihkan" aria-label="Pulihkan <?php echo e($customer->name); ?>">
                                                <i data-lucide="rotate-ccw" class="w-4.5 h-4.5"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo e(route('customers.force-destroy', $customer->id)); ?>" id="forceDeleteCustomer_<?php echo e($customer->id); ?>" class="hidden">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        </form>
                                        <button type="button" onclick="confirmForceDelete('forceDeleteCustomer_<?php echo e($customer->id); ?>', 'customer <?php echo e($customer->name); ?>')"
                                                class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                                title="Hapus Permanen"
                                                aria-label="Hapus Permanen <?php echo e($customer->name); ?>">
                                            <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                    <a href="<?php echo e(route('customers.show', $customer)); ?>" class="p-2 text-stone-400 hover:text-bark-dark hover:bg-cream/40 rounded-xl transition-all" title="Detail" aria-label="Detail <?php echo e($customer->name); ?>">
                                        <i data-lucide="eye" class="w-4.5 h-4.5"></i>
                                    </a>
                                    <a href="<?php echo e(route('customers.edit', $customer)); ?>" class="p-2 text-stone-400 hover:text-gold hover:bg-gold/10 rounded-xl transition-all" title="Edit" aria-label="Edit <?php echo e($customer->name); ?>">
                                        <i data-lucide="edit-3" class="w-4.5 h-4.5"></i>
                                    </a>
                                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', (str_starts_with($customer->phone, '0') ? '62'.substr($customer->phone,1) : $customer->phone))); ?>" target="_blank" class="p-2 text-stone-400 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all" title="WhatsApp" aria-label="WhatsApp <?php echo e($customer->name); ?>">
                                        <i data-lucide="message-square" class="w-4.5 h-4.5"></i>
                                    </a>
                                    <?php if(auth()->guard()->check()): ?>
                                    <?php if(auth()->user()->isSuperAdmin()): ?>
                                    <form method="POST" action="<?php echo e(route('customers.destroy', $customer)); ?>" id="deleteCustomer_<?php echo e($customer->id); ?>" class="hidden">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    </form>
                                    <button type="button" onclick="confirmDelete('deleteCustomer_<?php echo e($customer->id); ?>', 'customer <?php echo e($customer->name); ?>')"
                                            class="p-2 text-stone-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                            title="Hapus"
                                            aria-label="Hapus <?php echo e($customer->name); ?>">
                                        <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center bg-cream/10">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-16 h-16 rounded-full bg-gold/10 flex items-center justify-center text-gold mb-4 shadow-sm">
                                        <i data-lucide="users" class="w-8 h-8"></i>
                                    </div>
                                    <?php if(request('status') === 'deactivated'): ?>
                                    <h3 class="font-serif text-lg font-bold text-bark-dark mb-1">Tidak Ada Pelanggan Dinonaktifkan</h3>
                                    <p class="text-xs text-stone-400 mb-6 leading-relaxed">Semua pelanggan dalam status aktif.</p>
                                    <a href="<?php echo e(route('customers.index')); ?>" class="btn-secondary">
                                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                        Lihat Pelanggan Aktif
                                    </a>
                                    <?php else: ?>
                                    <h3 class="font-serif text-lg font-bold text-bark-dark mb-1">Belum Ada Pelanggan</h3>
                                    <p class="text-xs text-stone-400 mb-6 leading-relaxed">Mulai daftarkan data pelanggan pertama Anda untuk memulai manajemen penyewaan jas.</p>
                                    <a href="<?php echo e(route('customers.create')); ?>" class="btn-primary">
                                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                                        Tambah Pelanggan
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($customers->hasPages()): ?>
            <div class="px-6 py-4 border-t border-cream-sand/50 bg-cream/5">
                <?php echo e($customers->appends(request()->query())->links('components.pagination')); ?>

            </div>
            <?php endif; ?>
        </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function applyCustomerFilters() {
    const search = document.getElementById('search-customer')?.value || '';
    const status = document.getElementById('status-filter')?.value || 'active';
    const bl = document.getElementById('bl-filter')?.value || '';
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    params.set('status', status);
    if (bl) params.set('bl_status', bl);
    window.location.href = '?' + params.toString();
}

function confirmForceDelete(formId, label) {
    Swal.fire({
        title: 'Hapus Permanen',
        html: 'Apakah Anda yakin ingin menghapus permanen <strong>' + label + '</strong>?<br><br>Data yang dihapus <span class="text-red-600 font-bold">TIDAK DAPAT</span> dipulihkan kembali.',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Permanen',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-semibold',
            cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-semibold',
            popup: 'rounded-2xl'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });
            document.getElementById(formId).submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/customers/index.blade.php ENDPATH**/ ?>