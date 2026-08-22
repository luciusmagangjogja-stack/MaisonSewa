<?php $__env->startSection('title', 'Edit Produk — ' . $product->name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('products.show', $product)); ?>" class="btn-secondary p-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Edit Produk</h1>
                <p class="text-sm mt-0.5" style="color: var(--text-soft)"><?php echo e($product->code); ?> · <?php echo e($product->name); ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('products.show', $product)); ?>" class="btn-secondary">
                <i data-lucide="eye" class="w-4 h-4"></i> Lihat Detail
            </a>
        </div>
    </div>

    
    <form method="POST" action="<?php echo e(route('products.update', $product)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            
            <div class="lg:col-span-2 space-y-6">

                
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="info" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Informasi Dasar</h2>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="<?php echo e(old('name', $product->name)); ?>"
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

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                                Kode Produk <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" value="<?php echo e(old('code', $product->code)); ?>"
                                   class="form-input <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['code'];
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
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" class="form-input <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Pilih Kategori</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>"
                                        <?php echo e(old('category_id', $product->category_id) == $cat->id ? 'selected' : ''); ?>>
                                        <?php echo e($cat->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Deskripsi</label>
                        <textarea name="description" rows="4"
                                  class="form-input resize-none <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description', $product->description)); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <?php if(auth()->user()->isSuperAdmin()): ?>
                
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Cabang Tersedia</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" id="select-all-branches-edit" class="rounded text-blue-600 focus:ring-blue-500" <?php echo e(count($selectedBranches) === $branches->count() ? 'checked' : ''); ?>>
                            <span class="text-sm font-medium" style="color:var(--text-dark)">Pilih Semua Cabang</span>
                        </label>
                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 cursor-pointer hover:bg-slate-50">
                            <input type="checkbox" name="branch_ids[]" value="<?php echo e($branch->id); ?>" class="branch-checkbox-edit rounded text-blue-600 focus:ring-blue-500" <?php echo e(in_array($branch->id, old('branch_ids', $selectedBranches)) ? 'checked' : ''); ?>>
                            <span class="text-sm" style="color:var(--text-dark)"><?php echo e($branch->name); ?></span>
                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php $__errorArgs = ['branch_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php endif; ?>

                
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="shirt" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Detail Fisik</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Ukuran</label>
                            <select name="size" class="form-input <?php $__errorArgs = ['size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Pilih Ukuran</option>
                                <?php $__currentLoopData = ['XS','S','M','L','XL','XXL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($sz); ?>"
                                        <?php echo e(old('size', $product->size) === $sz ? 'selected' : ''); ?>><?php echo e($sz); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Warna</label>
                            <input type="text" name="color" value="<?php echo e(old('color', $product->color)); ?>"
                                   class="form-input <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Kondisi</label>
                            <select name="condition" class="form-input <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">Pilih Kondisi</option>
                                <?php $__currentLoopData = ['excellent','good','fair','poor']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cond): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cond); ?>"
                                        <?php echo e(old('condition', $product->condition) === $cond ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($cond)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['condition'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="card p-6 space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="package" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Stok & Harga</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">
                                Stok Total <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock_total" min="0"
                                   value="<?php echo e(old('stock_total', $product->stock_total)); ?>"
                                   class="form-input <?php $__errorArgs = ['stock_total'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['stock_total'];
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
                                Stok Tersedia <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock_available" min="0"
                                   value="<?php echo e(old('stock_available', $product->stock_available)); ?>"
                                   class="form-input <?php $__errorArgs = ['stock_available'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['stock_available'];
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
                                Harga Sewa/Hari <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium" style="color:var(--text-soft)">Rp</span>
                                <input type="number" name="rental_price" min="0"
                                       value="<?php echo e(old('rental_price', $product->rental_price)); ?>"
                                       class="form-input <?php $__errorArgs = ['rental_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="padding-left: 40px !important">
                            </div>
                            <?php $__errorArgs = ['rental_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

            </div>

            
            <div class="space-y-6">

                
                <div class="card p-6 space-y-4" x-data="productPhotoForm()">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="image" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Foto Produk</h2>
                    </div>

                    <div x-show="photoPreview || hasCurrentPhoto" class="relative" style="aspect-ratio: 3/4; max-height: 320px;">
                        <img :src="photoPreview || currentPhotoUrl" class="w-full h-full object-cover rounded-xl" alt="Preview foto produk">

                        <button type="button" @click="clearPhoto()"
                                class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center"
                                style="background: rgba(0,0,0,0.5)">
                            <i data-lucide="x" class="w-3.5 h-3.5 text-white"></i>
                        </button>

                        <button type="button" @click="$refs.photoInput.click()"
                                class="absolute bottom-2 right-2 btn-secondary text-xs px-2 py-1">
                            Ganti Foto
                        </button>
                    </div>

                    <div x-show="!photoPreview && !hasCurrentPhoto"
                         class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-colors"
                         style="border-color:var(--border)"
                         @click="$refs.photoInput.click()"
                         @dragover.prevent="isDragging = true"
                         @dragenter.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; handleDrop($event)"
                         :class="isDragging ? 'border-blue-500 bg-blue-50' : ''">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3" style="background:var(--secondary)">
                            <i data-lucide="upload-cloud" class="w-6 h-6" style="color:var(--primary)"></i>
                        </div>
                        <p class="text-sm font-medium" style="color:var(--text-dark)">Klik atau seret foto ke sini</p>
                        <p class="text-xs mt-1" style="color:var(--text-soft)">PNG, JPG, WEBP — Maks. 2MB</p>
                    </div>

                    <input type="file" x-ref="photoInput" name="image" accept="image/*" class="hidden"
                           @change="handlePhotoSelect($event)">
                    <?php $__errorArgs = ['image'];
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
                            <i data-lucide="toggle-right" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Status Produk</h2>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Status</label>
                        <?php if($product->status === 'rented'): ?>
                            <div class="form-input bg-slate-100 text-slate-500 cursor-not-allowed flex items-center justify-between">
                                <span>Disewa</span>
                                <span class="text-xs font-medium" style="color:var(--text-soft)">Otomatis — sedang dalam transaksi aktif</span>
                            </div>
                            <p class="text-xs mt-1" style="color:var(--text-soft)">Status otomatis berdasarkan rental aktif dan tidak dapat diubah manual.</p>
                        <?php else: ?>
                            <select name="status" class="form-input <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="available"   <?php echo e(old('status', $product->status) === 'available'   ? 'selected' : ''); ?>>Tersedia</option>
                                <option value="maintenance" <?php echo e(old('status', $product->status) === 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                                <option value="inactive"    <?php echo e(old('status', $product->status) === 'inactive'    ? 'selected' : ''); ?>>Tidak Aktif</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-500 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="card p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                            <i data-lucide="file-text" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                        </div>
                        <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Catatan Internal</h2>
                    </div>
                    <textarea name="notes" rows="3"
                              class="form-input resize-none text-sm"><?php echo e(old('notes', $product->notes)); ?></textarea>
                </div>

                
                <div class="card p-4">
                    <p class="text-xs" style="color:var(--text-soft)">
                        <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i>
                        Diperbarui: <?php echo e($product->updated_at->diffForHumans()); ?>

                    </p>
                    <p class="text-xs mt-1" style="color:var(--text-soft)">
                        <i data-lucide="calendar" class="w-3 h-3 inline-block mr-1"></i>
                        Dibuat: <?php echo e($product->created_at->format('d M Y')); ?>

                    </p>
                </div>

                
                <div class="flex flex-col gap-3">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                    <a href="<?php echo e(route('products.show', $product)); ?>" class="btn-secondary w-full justify-center text-center">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function productPhotoForm() {
    return {
        photoPreview: null,
        hasCurrentPhoto: <?php echo e($product->photo ? 'true' : 'false'); ?>,
        currentPhotoUrl: '<?php echo e($product->photo ? asset("storage/".$product->photo) : ""); ?>',
        isDragging: false,

        handlePhotoSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                this.photoPreview = e.target.result;
                this.hasCurrentPhoto = true;
            };
            reader.readAsDataURL(file);
        },

        handleDrop(event) {
            const file = event.dataTransfer.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                this.photoPreview = e.target.result;
                this.hasCurrentPhoto = true;
            };
            reader.readAsDataURL(file);
        },

        clearPhoto() {
            this.photoPreview = null;
            this.hasCurrentPhoto = false;
            this.$refs.photoInput.value = '';
        },
    };
}

// Select all branches toggle
const selectAllEdit = document.getElementById('select-all-branches-edit');
const branchCheckboxesEdit = document.querySelectorAll('.branch-checkbox-edit');
if (selectAllEdit) {
    selectAllEdit.addEventListener('change', function() {
        branchCheckboxesEdit.forEach(cb => cb.checked = this.checked);
    });
    branchCheckboxesEdit.forEach(cb => {
        cb.addEventListener('change', function() {
            selectAllEdit.checked = Array.from(branchCheckboxesEdit).every(c => c.checked);
        });
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/products/edit.blade.php ENDPATH**/ ?>