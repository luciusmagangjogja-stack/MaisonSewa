<?php $__env->startSection('title', 'Broadcast - SewaJas'); ?>
<?php $__env->startSection('page-title', 'Broadcast'); ?>
<?php $__env->startSection('subtitle', 'Kirim pengumuman internal ke admin cabang dan sales'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm lg:p-7">
        <div class="mb-6 flex items-start gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                <i data-lucide="send" class="h-6 w-6"></i>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-slate-950">Buat Broadcast</h2>
                <p class="mt-1 text-sm text-slate-500">Pesan akan masuk ke notifikasi penerima sesuai target yang dipilih.</p>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('broadcasts.store')); ?>" class="space-y-5" x-data="{ target: '<?php echo e(old('target', 'all')); ?>' }">
            <?php echo csrf_field(); ?>
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Judul</label>
                <input type="text" name="title" value="<?php echo e(old('title')); ?>" class="form-input" maxlength="120" placeholder="Contoh: Pengingat promo akhir pekan" required>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">Pesan</label>
                <textarea name="message" rows="6" class="form-input" maxlength="1200" placeholder="Tulis pengumuman yang singkat, jelas, dan bisa langsung ditindaklanjuti." required><?php echo e(old('message')); ?></textarea>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200" :class="target === 'all' ? 'border-blue-300 bg-blue-50/70' : 'bg-white'">
                    <input type="radio" name="target" value="all" class="sr-only" x-model="target">
                    <div class="flex items-center gap-2 font-bold text-slate-900">
                        <i data-lucide="users" class="h-4 w-4 text-blue-700"></i>
                        Semua
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Semua user aktif dalam scope akses.</p>
                </label>

                <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200" :class="target === 'role' ? 'border-blue-300 bg-blue-50/70' : 'bg-white'">
                    <input type="radio" name="target" value="role" class="sr-only" x-model="target">
                    <div class="flex items-center gap-2 font-bold text-slate-900">
                        <i data-lucide="badge-check" class="h-4 w-4 text-blue-700"></i>
                        Role
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Kirim hanya ke role tertentu.</p>
                </label>

                <label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200" :class="target === 'branch' ? 'border-blue-300 bg-blue-50/70' : 'bg-white'">
                    <input type="radio" name="target" value="branch" class="sr-only" x-model="target">
                    <div class="flex items-center gap-2 font-bold text-slate-900">
                        <i data-lucide="building-2" class="h-4 w-4 text-blue-700"></i>
                        Cabang
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Kirim ke semua user di cabang.</p>
                </label>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div x-show="target === 'role'" x-transition>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Pilih Role</label>
                    <select name="role" class="form-input">
                        <option value="">Pilih role</option>
                        <?php $__currentLoopData = $roleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(old('role') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div x-show="target === 'branch'" x-transition>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Pilih Cabang</label>
                    <select name="branch_id" class="form-input">
                        <option value="">Pilih cabang</option>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($branch->id); ?>" <?php if((string) old('branch_id') === (string) $branch->id): echo 'selected'; endif; ?>><?php echo e($branch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">Link Aksi Opsional</label>
                    <input type="url" name="action_url" value="<?php echo e(old('action_url')); ?>" class="form-input" placeholder="https://...">
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-end">
                <button type="submit" class="btn-primary px-6">
                    <i data-lucide="send" class="h-4 w-4"></i>
                    Kirim Broadcast
                </button>
            </div>
        </form>
    </section>

    <aside class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm lg:p-6">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-slate-950">Riwayat Terakhir</h2>
                <p class="mt-1 text-xs text-slate-500">Notifikasi broadcast yang baru dikirim.</p>
            </div>
            <span class="badge badge-blue"><?php echo e($broadcasts->count()); ?></span>
        </div>

        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $broadcasts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $broadcast): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                            <i data-lucide="radio" class="h-4 w-4"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="line-clamp-2 text-sm font-bold text-slate-950"><?php echo e(str_replace('Broadcast: ', '', $broadcast->title)); ?></h3>
                            <p class="mt-1 line-clamp-3 text-xs leading-5 text-slate-500"><?php echo e($broadcast->message); ?></p>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold text-slate-400">
                                <span><?php echo e(optional($broadcast->created_at)->format('d M Y H:i')); ?></span>
                                <?php if(is_array($broadcast->meta) && isset($broadcast->meta['recipient_count'])): ?>
                                    <span>· <?php echo e($broadcast->meta['recipient_count']); ?> penerima</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                    <i data-lucide="inbox" class="mx-auto mb-3 h-8 w-8 text-slate-300"></i>
                    <p class="text-sm font-semibold text-slate-500">Belum ada broadcast.</p>
                </div>
            <?php endif; ?>
        </div>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/broadcasts/index.blade.php ENDPATH**/ ?>