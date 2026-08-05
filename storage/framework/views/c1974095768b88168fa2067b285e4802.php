<?php $__env->startSection('title','Kelola Pengguna'); ?>
<?php $__env->startSection('page-title','Kelola Pengguna'); ?>
<?php $__env->startSection('subtitle','Manajemen akun & hak akses — Super Admin Only'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-5">

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php $__currentLoopData = [
            ['role'=>'super_admin','label'=>'Super Admin','color'=>'#D6B98C','bg'=>'#D6B98C15','icon'=>'shield-check','desc'=>'Akses penuh semua cabang'],
            ['role'=>'admin_toko', 'label'=>'Admin Toko', 'color'=>'#3B82F6','bg'=>'#3B82F615','icon'=>'store',       'desc'=>'Kelola satu cabang'],
            ['role'=>'sales',      'label'=>'Sales',       'color'=>'#10B981','bg'=>'#10B98115','icon'=>'user-check',  'desc'=>'Transaksi harian saja'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card p-4 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:<?php echo e($r['bg']); ?>">
                <i data-lucide="<?php echo e($r['icon']); ?>" class="w-5 h-5" style="color:<?php echo e($r['color']); ?>"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm" style="color:var(--text-dark)">
                    <?php echo e($roleCounts[$r['role']] ?? 0); ?>

                    <span style="color:<?php echo e($r['color']); ?>"><?php echo e($r['label']); ?></span>
                </p>
                <p class="text-xs" style="color:var(--text-soft)"><?php echo e($r['desc']); ?></p>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="<?php echo e(route('users.index')); ?>" class="flex flex-wrap gap-3 items-end">
            <div>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Cari nama, email..."
                       class="form-input">
            </div>
            <div>
                <select name="role" class="form-input">
                    <option value="">Semua Role</option>
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role); ?>" <?php echo e(request('role') === $role ? 'selected' : ''); ?>>
                        <?php echo e(ucfirst(str_replace('_', ' ', $role))); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <select name="branch" class="form-input">
                    <option value="">Semua Cabang</option>
                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($b->id); ?>" <?php echo e(request('branch') == $b->id ? 'selected' : ''); ?>><?php echo e($b->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn-secondary">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter
            </button>
            <?php if(request()->hasAny(['search','role','branch'])): ?>
            <a href="<?php echo e(route('users.index')); ?>" class="btn-secondary">
                <i data-lucide="x" class="w-4 h-4"></i> Reset
            </a>
            <?php endif; ?>
        </form>
        <a href="<?php echo e(route('users.create')); ?>" class="btn-primary">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            Tambah Pengguna
        </a>
    </div>

    
    <div class="card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-lg transition-all duration-300">
        <?php if($users->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--secondary)">
                <i data-lucide="users" class="w-8 h-8" style="color:var(--primary)"></i>
            </div>
            <p class="font-semibold text-lg" style="color:var(--text-dark)">Belum ada pengguna</p>
            <p class="text-sm mt-1 mb-4" style="color:var(--text-soft)">Tambahkan pengguna pertama</p>
            <a href="<?php echo e(route('users.create')); ?>" class="btn-primary">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
            </a>
        </div>
        <?php else: ?>
        
        <div class="hidden md:block">
            <table class="w-full elegant-table">
                <thead>
                    <tr>
                        <th class="text-left">#</th>
                        <th class="text-left">Pengguna</th>
                        <th class="text-left">Role</th>
                        <th class="text-left">Cabang</th>
                        <th class="text-left">Kontak</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e(!$user->is_active ? 'opacity-60' : ''); ?>">
                        <td class="text-xs" style="color:var(--text-soft)"><?php echo e($users->firstItem() + $loop->index); ?></td>

                        
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="<?php echo e($user->avatar_url); ?>" class="w-9 h-9 rounded-xl object-cover"
                                     style="outline: 2px solid #ffffff; outline-offset: 0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.10);">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm" style="color:var(--text-dark)">
                                        <?php echo e($user->name); ?>

                                        <?php if($user->id === auth()->id()): ?>
                                        <span class="badge badge-gold text-[9px] ml-1">Anda</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-xs truncate" style="color:var(--text-soft)"><?php echo e($user->email); ?></p>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge text-[11px] px-3 py-1
                                <?php echo e(match($user->role) {
                                    'super_admin' => 'badge-gold',
                                    'admin_toko'  => 'badge-blue',
                                    'sales'       => 'badge-green',
                                    default       => 'badge-gray'
                                }); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $user->role ?? '-'))); ?>

                            </span>
                        </td>

                        <td>
                            <?php if($user->branch): ?>
                                <p class="text-sm" style="color:var(--text-dark)"><?php echo e($user->branch->name); ?></p>
                                <p class="text-xs" style="color:var(--text-soft)"><?php echo e($user->branch->code); ?></p>
                            <?php elseif($user->role === 'super_admin'): ?>
                                <span class="text-xs" style="color:var(--primary)">Semua Cabang</span>
                            <?php else: ?>
                                <span class="text-xs text-red-400">Belum dikaitkan</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-sm" style="color:var(--text-soft)"><?php echo e($user->phone ?? '-'); ?></td>

                        <td class="text-center">
                            <span class="badge <?php echo e($user->is_active ? 'badge-green' : 'badge-red'); ?>">
                                <?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?>

                            </span>
                        </td>

                        
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="<?php echo e(route('users.edit', $user)); ?>"
                                   class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Edit"
                                   style="color:var(--text-soft)">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </a>
                                <?php if($user->id !== auth()->id()): ?>
                                <form method="POST" action="<?php echo e(route('users.toggle', $user)); ?>" id="toggleUserIndex_<?php echo e($user->id); ?>" class="hidden">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                </form>
                                <button type="button" onclick="confirmAction('toggleUserIndex_<?php echo e($user->id); ?>', '<?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?> Pengguna', '<?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?> pengguna <?php echo e($user->name); ?>?', '<?php echo e($user->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan'); ?>', '<?php echo e($user->is_active ? '#ef4444' : '#10b981'); ?>')"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                                        title="<?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>"
                                        style="color:<?php echo e($user->is_active ? '#C0392B' : '#10B981'); ?>">
                                    <i data-lucide="<?php echo e($user->is_active ? 'user-x' : 'user-check'); ?>" class="w-3.5 h-3.5"></i>
                                </button>
                                <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" id="deleteUserIndex_<?php echo e($user->id); ?>" class="hidden">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                </form>
                                <button type="button" onclick="confirmDelete('deleteUserIndex_<?php echo e($user->id); ?>', 'pengguna <?php echo e($user->name); ?>')"
                                        class="p-1.5 rounded-lg hover:bg-red-50 transition-colors"
                                        title="Nonaktifkan/Hapus"
                                        style="color:#C0392B">
                                    <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <div class="md:hidden space-y-3">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card border rounded-xl p-4 space-y-3">
                
                <div class="flex items-center gap-3">
                    <img src="<?php echo e($user->avatar_url); ?>" class="w-10 h-10 rounded-xl object-cover flex-shrink-0"
                         style="outline: 2px solid #ffffff; outline-offset: 0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.10);">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate" style="color:var(--text-dark)">
                            <?php echo e($user->name); ?>

                            <?php if($user->id === auth()->id()): ?>
                            <span class="badge badge-gold text-[9px] ml-1">Anda</span>
                            <?php endif; ?>
                        </p>
                        <p class="text-xs truncate" style="color:var(--text-soft)"><?php echo e($user->email); ?></p>
                    </div>
                    <span class="badge text-[11px] px-3 py-1 flex-shrink-0
                        <?php echo e(match($user->role) {
                            'super_admin' => 'badge-gold',
                            'admin_toko'  => 'badge-blue',
                            'sales'       => 'badge-green',
                            default       => 'badge-gray'
                        }); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $user->role ?? '-'))); ?>

                    </span>
                </div>

                
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs" style="color:var(--text-soft)">
                    <span>
                        <?php if($user->branch): ?>
                            <strong class="font-medium" style="color:var(--text-dark)"><?php echo e($user->branch->name); ?></strong>
                            <span> - <?php echo e($user->branch->code); ?></span>
                        <?php elseif($user->role === 'super_admin'): ?>
                            <span style="color:var(--primary)">Semua Cabang</span>
                        <?php else: ?>
                            <span class="text-red-400">Belum dikaitkan</span>
                        <?php endif; ?>
                    </span>
                    <span><?php echo e($user->phone ?? '-'); ?></span>
                    <span class="badge <?php echo e($user->is_active ? 'badge-green' : 'badge-red'); ?> text-[11px]">
                        <?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?>

                    </span>
                </div>

                
                <div class="flex items-center gap-2 pt-2 border-t" style="border-color:var(--border)">
                    <a href="<?php echo e(route('users.edit', $user)); ?>"
                       class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                       style="background:var(--secondary); color:var(--text-dark)">
                        <i data-lucide="edit-2" class="w-4 h-4"></i> Edit
                    </a>
                    <?php if($user->id !== auth()->id()): ?>
                    <form method="POST" action="<?php echo e(route('users.toggle', $user)); ?>" id="toggleUserMobile_<?php echo e($user->id); ?>" class="hidden">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    </form>
                    <button type="button"
                            onclick="confirmAction('toggleUserMobile_<?php echo e($user->id); ?>', '<?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?> Pengguna', '<?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?> pengguna <?php echo e($user->name); ?>?', '<?php echo e($user->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan'); ?>', '<?php echo e($user->is_active ? '#ef4444' : '#10b981'); ?>')"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px]"
                            style="background:var(--secondary); color:var(--text-dark)">
                        <i data-lucide="<?php echo e($user->is_active ? 'user-x' : 'user-check'); ?>" class="w-4 h-4"></i>
                        <?php echo e($user->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>

                    </button>
                    <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" id="deleteUserMobile_<?php echo e($user->id); ?>" class="hidden">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    </form>
                    <button type="button"
                            onclick="confirmDelete('deleteUserMobile_<?php echo e($user->id); ?>', 'pengguna <?php echo e($user->name); ?>')"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-sm font-medium transition-colors min-h-[44px] text-red-600 hover:bg-red-50"
                            title="Hapus">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
        <?php if($users->hasPages()): ?>
        <div class="px-6 py-4 border-t" style="border-color:var(--border)">
            <?php echo e($users->links('components.pagination')); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>lucide.createIcons();</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/users/index.blade.php ENDPATH**/ ?>