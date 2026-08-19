<?php $__env->startSection('title', isset($customer) ? 'Edit Customer' : 'Tambah Customer'); ?>
<?php $__env->startSection('page-title', isset($customer) ? 'Edit Customer' : 'Tambah Customer Baru'); ?>
<?php $__env->startSection('subtitle', isset($customer) ? $customer->name : 'Input data pelanggan baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-5">

<div class="flex items-center gap-3 mb-5">
        <a href="<?php echo e(isset($customer) ? route('customers.show', $customer) : route('customers.index')); ?>" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <?php echo e(isset($customer) ? 'Kembali ke Detail Customer' : 'Kembali ke Customer'); ?>

        </a>
    </div>

    <?php
        $phoneOld = old('phone', $customer->phone ?? '');
    ?>


    <form method="POST" action="<?php echo e(isset($customer) ? route('customers.update', $customer) : route('customers.store')); ?>"
          enctype="multipart/form-data" x-data="customerForm()">
        <?php echo csrf_field(); ?>
        <?php if(isset($customer)): ?> <?php echo method_field('PATCH'); ?> <?php endif; ?>

        <!-- Basic Info -->
        <div class="ds-card p-6">
            <h3 class="font-semibold text-base mb-5" style="color: var(--text-dark)">
                <i data-lucide="user" class="w-4 h-4 inline mr-2" style="color: var(--primary)"></i>
                Informasi Dasar
            </h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="<?php echo e(old('name', $customer->name ?? '')); ?>"
                           class="form-input" placeholder="Nama lengkap customer" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

<div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">Nomor Handphone <span class="text-red-400">*</span></label>
                    <div class="input-group-phone">
                        <span class="input-group-phone__prefix">+62</span>
<input type="text"
                               name="phone"
                               value="<?php echo e(old('phone', $customer->phone ?? '')); ?>"
                               class="input-group-phone__input"
                               placeholder="8123456789"
                               required
                               inputmode="numeric"
                               x-on:input="filterPhoneInput($event)"
                               x-on:keydown="preventNonNumeric($event)"
                               autocomplete="tel">
                    </div>
                    <p class="text-xs text-stone-500 mt-1" style="color: var(--text-soft)">Format: angka saja. Prefix +62 akan disimpan otomatis.</p>
                     <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <?php if(session('deleted_customer')): ?>
                        <div class="mt-3 ds-card p-4 bg-amber-50 border border-amber-200">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-100">
                                    <i data-lucide="info" class="w-5 h-5 text-amber-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-amber-900">Customer Found</p>
                                    <div class="mt-2 space-y-1 text-sm text-amber-800">
                                        <p><span class="font-medium">Name:</span> <?php echo e(session('deleted_customer.name')); ?></p>
                                        <p><span class="font-medium">Phone:</span> <?php echo e(session('deleted_customer.phone')); ?></p>
                                        <p><span class="font-medium">Deleted At:</span> <?php echo e(session('deleted_customer.deleted_at')); ?></p>
                                    </div>
                                    <p class="text-xs text-amber-700 mt-2">This customer has been deactivated.</p>
                                    <form method="POST" action="<?php echo e(route('customers.restore', session('deleted_customer.id'))); ?>" class="mt-3">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-primary">
                                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            Restore Customer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>


                <!-- Disederhanakan: hanya Name + Phone. Kolom lain disembunyikan di UI (tetap ada di database) -->
                <div class="sm:col-span-2">
                    <div class="rounded-xl border border-cream-sand/70 bg-cream/20 p-4">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-4 h-4 text-stone-400 mt-0.5"></i>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-bark-dark">Identitas utama</p>
                                <p class="text-xs text-stone-500 mt-0.5">Hanya Nama dan Nomor Handphone yang ditampilkan di form ini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between">
            <a href="<?php echo e(isset($customer) ? route('customers.show', $customer) : route('customers.index')); ?>" class="btn-secondary">
                <i data-lucide="x" class="w-4 h-4"></i>
                Batal
            </a>
            <button type="submit" class="btn-primary px-8">
                <i data-lucide="<?php echo e(isset($customer) ? 'save' : 'user-plus'); ?>" class="w-4 h-4"></i>
                <?php echo e(isset($customer) ? 'Simpan Perubahan' : 'Tambah Customer'); ?>

            </button>
        </div>

    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function customerForm() {
    return {
        idPhotoPreview: null,
        hasCurrentIdPhoto: <?php echo e(isset($customer) && $customer->id_photo ? 'true' : 'false'); ?>,
        currentIdPhotoUrl: '<?php echo e(isset($customer) && $customer->id_photo ? asset("storage/".$customer->id_photo) : ""); ?>',

        preventNonNumeric(event) {
            // Allow: backspace, delete, tab, escape, enter
            if ([8, 46, 9, 27, 13].includes(event.keyCode) ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (event.keyCode === 65 && (event.ctrlKey || event.metaKey)) ||
                (event.keyCode === 67 && (event.ctrlKey || event.metaKey)) ||
                (event.keyCode === 86 && (event.ctrlKey || event.metaKey)) ||
                (event.keyCode === 88 && (event.ctrlKey || event.metaKey)) ||
                // Allow: home, end, left, right
                (event.keyCode >= 35 && event.keyCode <= 39)) {
                return; // let it happen, don't do anything
            }

            // Ensure that it is a number and stop the keypress
            if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) && (event.keyCode < 96 || event.keyCode > 105)) {
                event.preventDefault();
            }
        },

        filterPhoneInput(event) {
            const input = event.target;
            let digits = input.value.replace(/[^0-9]/g, ''); // Keep only digits

            // If user types 08..., convert to 62... (without leading 0)
            if (digits.startsWith('0')) {
                digits = '62' + digits.slice(1);
            }

            input.value = digits;
        },

        previewIdPhoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                this.idPhotoPreview = e.target.result;
                this.hasCurrentIdPhoto = true;
            };
            reader.readAsDataURL(file);
        },

        clearIdPhoto() {
            this.idPhotoPreview = null;
            this.hasCurrentIdPhoto = false;
            this.$refs.idPhotoInput.value = '';
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/customers/create.blade.php ENDPATH**/ ?>