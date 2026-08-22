<?php $__env->startSection('title','Edit Pengguna — ' . $user->name); ?>
<?php $__env->startSection('page-title','Edit Pengguna'); ?>
<?php $__env->startSection('subtitle','Ubah data akun pengguna'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('users.show', $user)); ?>" class="btn-secondary p-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="font-playfair text-2xl font-bold" style="color:var(--text-dark)">Edit Pengguna</h1>
                <p class="text-sm" style="color:var(--text-soft)"><?php echo e($user->email); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="badge <?php echo e($user->is_active ? 'badge-green' : 'badge-red'); ?>">
                <?php echo e($user->is_active ? 'Aktif' : 'Nonaktif'); ?>

            </span>
            <span class="badge
                <?php echo e(match($user->role) {
                    'super_admin' => 'badge-gold',
                    'admin_toko'  => 'badge-blue',
                    'sales'       => 'badge-green',
                    default       => 'badge-gray'
                }); ?>">
                <?php echo e(ucfirst(str_replace('_', ' ', $user->role ?? '-'))); ?>

            </span>
        </div>
    </div>

    
    <form method="POST" action="<?php echo e(route('users.update', $user)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            
            <div class="lg:col-span-2 space-y-6">

                
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="user" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Informasi Akun</h2>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>"
                               class="form-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-soft)"></i>
                            <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>"
                                   class="form-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="padding-left: 40px !important">
                        </div>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">No. Telepon</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-soft)"></i>
                            <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>"
                                   class="form-input <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="padding-left: 40px !important">
                        </div>
                        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <?php if($user->role === 'sales'): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                                Rate Komisi Serah Jas (%) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="commission_rate_serah" value="<?php echo e(old('commission_rate_serah', $user->commission_rate_serah ?? 5)); ?>" min="0" step="0.01"
                                       class="form-input pr-10 <?php $__errorArgs = ['commission_rate_serah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="padding-right: 40px !important">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-medium" style="color:var(--text-soft)">%</span>
                            </div>
                            <p class="text-xs mt-1" style="color:var(--text-soft)">Komisi saat serah jas ke customer.</p>
                            <?php $__errorArgs = ['commission_rate_serah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                                Rate Komisi Pengembalian (%) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="commission_rate_kembali" value="<?php echo e(old('commission_rate_kembali', $user->commission_rate_kembali ?? 5)); ?>" min="0" step="0.01"
                                       class="form-input pr-10 <?php $__errorArgs = ['commission_rate_kembali'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="padding-right: 40px !important">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-medium" style="color:var(--text-soft)">%</span>
                            </div>
                            <p class="text-xs mt-1" style="color:var(--text-soft)">Komisi saat customer mengembalikan jas.</p>
                            <?php $__errorArgs = ['commission_rate_kembali'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="lock" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <div>
                            <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Ganti Password</h2>
                            <p class="text-xs mt-0.5" style="color:var(--text-soft)">Kosongkan jika tidak ingin mengubah password</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                       placeholder="Min. 8 karakter"
                                       class="form-input pr-10 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <button type="button" onclick="togglePassword('password','eye-password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i data-lucide="eye" id="eye-password" class="w-4 h-4" style="color:var(--text-soft)"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       placeholder="Ulangi password baru"
                                       class="form-input pr-10">
                                <button type="button" onclick="togglePassword('password_confirmation','eye-confirm')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <i data-lucide="eye" id="eye-confirm" class="w-4 h-4" style="color:var(--text-soft)"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            
            <div class="space-y-6">

                
                <div class="card p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="image" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Foto Profil</h2>
                    </div>

                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden relative group cursor-pointer"
                             onclick="document.getElementById('avatar-input').click()">
                            <img id="avatar-preview"
                                 src="<?php echo e($user->avatar_url); ?>"
                                 alt="<?php echo e($user->name); ?>"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                        <div class="w-full border-2 border-dashed rounded-xl p-3 text-center cursor-pointer"
                             style="border-color:var(--border)"
                             onclick="document.getElementById('avatar-input').click()">
                            <p class="text-xs" style="color:var(--text-soft)">
                                <span class="font-medium" style="color:var(--primary)">Klik</span> untuk ganti foto
                            </p>
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden">
                        </div>
                    </div>
                    <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="card p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="shield" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Hak Akses</h2>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" id="role-select"
                                onchange="toggleBranch(this.value)"
                                class="form-input <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role); ?>" <?php echo e(old('role', $user->role) === $role ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst(str_replace('_', ' ', $role))); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <div id="role-info" class="mt-2 rounded-xl p-3 text-xs hidden" style="background:var(--secondary)">
                            <p id="role-info-text" style="color:var(--text-soft)"></p>
                        </div>
                    </div>

                    
                    <div id="branch-wrap">
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Cabang</label>
                        <select name="branch_id" class="form-input <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Pilih Cabang</option>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($b->id); ?>" <?php echo e(old('branch_id', $user->branch_id) == $b->id ? 'selected' : ''); ?>>
                                <?php echo e($b->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['branch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="card p-5 space-y-3">
                    <div class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--text-soft)"></i>
                        <div>
                            <p class="text-xs" style="color:var(--text-soft)">Terakhir diperbarui</p>
                            <p class="text-sm font-medium" style="color:var(--text-dark)"><?php echo e($user->updated_at->diffForHumans()); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 flex-shrink-0" style="color:var(--text-soft)"></i>
                        <div>
                            <p class="text-xs" style="color:var(--text-soft)">Terdaftar sejak</p>
                            <p class="text-sm font-medium" style="color:var(--text-dark)"><?php echo e($user->created_at->format('d M Y')); ?></p>
                        </div>
                    </div>
                </div>

                
                <div class="flex flex-col gap-3">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
<a href="<?php echo e(route('users.show', $user)); ?>" class="btn-secondary w-full justify-center text-center">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();

    // Avatar preview
    document.getElementById('avatar-input').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // Toggle password visibility
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    // Toggle cabang berdasarkan role
    const roleDesc = {
        'super_admin': 'Akses penuh ke semua cabang. Tidak perlu memilih cabang.',
        'admin_toko' : 'Mengelola satu cabang. Wajib memilih cabang.',
        'sales'      : 'Hanya transaksi harian. Wajib memilih cabang.',
    };

    function toggleBranch(role) {
        const wrap = document.getElementById('branch-wrap');
        const info = document.getElementById('role-info');
        const text = document.getElementById('role-info-text');

        wrap.style.opacity       = role === 'super_admin' ? '0.4' : '1';
        wrap.style.pointerEvents = role === 'super_admin' ? 'none' : 'auto';

        if (roleDesc[role]) {
            text.textContent = roleDesc[role];
            info.classList.remove('hidden');
        } else {
            info.classList.add('hidden');
        }
    }

    // Trigger on load
    toggleBranch(document.getElementById('role-select').value);
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/users/edit.blade.php ENDPATH**/ ?>