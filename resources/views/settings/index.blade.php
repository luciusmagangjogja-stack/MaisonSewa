@extends('Layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Pengaturan</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Kelola QRIS, rekening, informasi perusahaan, dan branding aplikasi</p>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @include('components.flash-messages')

    {{-- FORM --}}
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ─── QRIS PEMBAYARAN ─────────────────────────────── --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                    <i data-lucide="qr-code" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                </div>
                <h2 class="font-semibold text-sm" style="color:var(--text-dark)">QRIS Pembayaran</h2>
            </div>

            @php
                $qrisPath = optional($settings->firstWhere('key', 'qris_image'))->value;
                $qrisUrl = '';
                if ($qrisPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($qrisPath)) {
                    $fullPath = storage_path('app/public/' . $qrisPath);
                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $qrisUrl = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($fullPath));
                }
            @endphp

            <div x-data="imageUploadForm('{{ $qrisUrl }}')" class="space-y-3">
                <div x-show="photoPreview || hasCurrentPhoto" class="relative" style="max-width: 280px;">
                    <img :src="photoPreview || currentPhotoUrl" class="w-full rounded-xl border" style="border-color:var(--border)" alt="Preview QRIS">
                    <button type="button" @click="clearPhoto()"
                            class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background: rgba(0,0,0,0.5)">
                        <i data-lucide="x" class="w-3.5 h-3.5 text-white"></i>
                    </button>
                </div>

                <div x-show="!photoPreview && !hasCurrentPhoto"
                     class="border-2 border-dashed rounded-xl p-5 text-center cursor-pointer transition-colors"
                     style="border-color:var(--border); max-width: 280px;"
                     @click="$refs.qrisInput.click()"
                     @dragover.prevent="isDragging = true"
                     @dragenter.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; handleDrop($event)"
                     :class="isDragging ? 'border-blue-500 bg-blue-50' : ''">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2" style="background:var(--secondary)">
                        <i data-lucide="upload-cloud" class="w-5 h-5" style="color:var(--primary)"></i>
                    </div>
                    <p class="text-sm font-medium" style="color:var(--text-dark)">Klik atau seret gambar QRIS</p>
                    <p class="text-xs mt-1" style="color:var(--text-soft)">PNG, JPG, WEBP — Maks. 2MB</p>
                </div>

                <input type="file" x-ref="qrisInput" name="qris_image" accept="image/*" class="hidden"
                       @change="handlePhotoSelect($event)">
                @error('qris_image')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ─── INFORMASI REKENING TRANSFER ─────────────────── --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                    <i data-lucide="landmark" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                </div>
                <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Informasi Rekening Transfer</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Nama Bank</label>
                    <input type="text" name="bank_name"
                           value="{{ old('bank_name', optional($settings->firstWhere('key', 'bank_name'))->value) }}"
                           class="form-input" placeholder="Contoh: BCA">
                    @error('bank_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Nomor Rekening</label>
                    <input type="text" name="bank_account"
                           value="{{ old('bank_account', optional($settings->firstWhere('key', 'bank_account'))->value) }}"
                           class="form-input" placeholder="Contoh: 1234567890">
                    @error('bank_account')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Atas Nama</label>
                    <input type="text" name="bank_holder"
                           value="{{ old('bank_holder', optional($settings->firstWhere('key', 'bank_holder'))->value) }}"
                           class="form-input" placeholder="Contoh: PT SewaJas Indonesia">
                    @error('bank_holder')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ─── INFORMASI PERUSAHAAN ─────────────────────────── --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                    <i data-lucide="building" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                </div>
                <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Informasi Perusahaan</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Nama Perusahaan</label>
                    <input type="text" name="company_name"
                           value="{{ old('company_name', optional($settings->firstWhere('key', 'company_name'))->value) }}"
                           class="form-input" placeholder="Contoh: PT SewaJas Indonesia">
                    @error('company_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Tagline</label>
                    <input type="text" name="company_tagline"
                           value="{{ old('company_tagline', optional($settings->firstWhere('key', 'company_tagline'))->value) }}"
                           class="form-input" placeholder="Contoh: Premium Suit Rental">
                    @error('company_tagline')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Alamat</label>
                <textarea name="company_address" rows="2" class="form-input resize-none"
                          placeholder="Alamat lengkap perusahaan">{{ old('company_address', optional($settings->firstWhere('key', 'company_address'))->value) }}</textarea>
                @error('company_address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Telepon</label>
                    <input type="text" name="company_phone"
                           value="{{ old('company_phone', optional($settings->firstWhere('key', 'company_phone'))->value) }}"
                           class="form-input" placeholder="Contoh: 021-12345678">
                    @error('company_phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Email</label>
                    <input type="email" name="company_email"
                           value="{{ old('company_email', optional($settings->firstWhere('key', 'company_email'))->value) }}"
                           class="form-input" placeholder="Contoh: info@sewajas.id">
                    @error('company_email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Website</label>
                    <input type="text" name="company_website"
                           value="{{ old('company_website', optional($settings->firstWhere('key', 'company_website'))->value) }}"
                           class="form-input" placeholder="Contoh: www.sewajas.id">
                    @error('company_website')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ─── LOGO PERUSAHAAN (INVOICE) ───────────────────── --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                    <i data-lucide="file-text" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                </div>
                <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Logo Perusahaan <span class="font-normal" style="color:var(--text-soft)">(untuk invoice)</span></h2>
            </div>

            @php
                $companyLogoPath = optional($settings->firstWhere('key', 'company_logo'))->value;
                $companyLogoUrl = '';
                if ($companyLogoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($companyLogoPath)) {
                    $fullPath = storage_path('app/public/' . $companyLogoPath);
                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $companyLogoUrl = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($fullPath));
                }
            @endphp

            <div x-data="imageUploadForm('{{ $companyLogoUrl }}')" class="space-y-3">
                <div x-show="photoPreview || hasCurrentPhoto" class="relative" style="max-width: 200px;">
                    <img :src="photoPreview || currentPhotoUrl" class="w-full rounded-xl border" style="border-color:var(--border)" alt="Preview Logo Perusahaan">
                    <button type="button" @click="clearPhoto()"
                            class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background: rgba(0,0,0,0.5)">
                        <i data-lucide="x" class="w-3.5 h-3.5 text-white"></i>
                    </button>
                </div>

                <div x-show="!photoPreview && !hasCurrentPhoto"
                     class="border-2 border-dashed rounded-xl p-5 text-center cursor-pointer transition-colors"
                     style="border-color:var(--border); max-width: 280px;"
                     @click="$refs.companyLogoInput.click()"
                     @dragover.prevent="isDragging = true"
                     @dragenter.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; handleDrop($event)"
                     :class="isDragging ? 'border-blue-500 bg-blue-50' : ''">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2" style="background:var(--secondary)">
                        <i data-lucide="upload-cloud" class="w-5 h-5" style="color:var(--primary)"></i>
                    </div>
                    <p class="text-sm font-medium" style="color:var(--text-dark)">Klik atau seret logo perusahaan</p>
                    <p class="text-xs mt-1" style="color:var(--text-soft)">PNG, JPG, WEBP — Maks. 2MB</p>
                </div>

                <input type="file" x-ref="companyLogoInput" name="company_logo" accept="image/*" class="hidden"
                       @change="handlePhotoSelect($event)">
                @error('company_logo')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ─── LOGO & NAMA APLIKASI ─────────────────────────── --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                    <i data-lucide="palette" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                </div>
                <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Logo & Nama Aplikasi <span class="font-normal" style="color:var(--text-soft)">(sidebar & login)</span></h2>
            </div>

            @php
                $appLogoPath = optional($settings->firstWhere('key', 'app_logo'))->value;
                $appLogoUrl = '';
                if ($appLogoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogoPath)) {
                    $fullPath = storage_path('app/public/' . $appLogoPath);
                    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $appLogoUrl = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($fullPath));
                }
            @endphp

            <div x-data="imageUploadForm('{{ $appLogoUrl }}')" class="space-y-3">
                <div x-show="photoPreview || hasCurrentPhoto" class="relative" style="max-width: 120px;">
                    <img :src="photoPreview || currentPhotoUrl" class="w-full rounded-xl border" style="border-color:var(--border)" alt="Preview Logo Aplikasi">
                    <button type="button" @click="clearPhoto()"
                            class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background: rgba(0,0,0,0.5)">
                        <i data-lucide="x" class="w-3.5 h-3.5 text-white"></i>
                    </button>
                </div>

                <div x-show="!photoPreview && !hasCurrentPhoto"
                     class="border-2 border-dashed rounded-xl p-5 text-center cursor-pointer transition-colors"
                     style="border-color:var(--border); max-width: 280px;"
                     @click="$refs.appLogoInput.click()"
                     @dragover.prevent="isDragging = true"
                     @dragenter.prevent="isDragging = true"
                     @dragleave.prevent="isDragging = false"
                     @drop.prevent="isDragging = false; handleDrop($event)"
                     :class="isDragging ? 'border-blue-500 bg-blue-50' : ''">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2" style="background:var(--secondary)">
                        <i data-lucide="upload-cloud" class="w-5 h-5" style="color:var(--primary)"></i>
                    </div>
                    <p class="text-sm font-medium" style="color:var(--text-dark)">Klik atau seret logo aplikasi</p>
                    <p class="text-xs mt-1" style="color:var(--text-soft)">PNG, JPG, WEBP — Maks. 2MB</p>
                </div>

                <input type="file" x-ref="appLogoInput" name="app_logo" accept="image/*" class="hidden"
                       @change="handlePhotoSelect($event)">
                @error('app_logo')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Nama Aplikasi</label>
                    <input type="text" name="app_name"
                           value="{{ old('app_name', optional($settings->firstWhere('key', 'app_name'))->value) }}"
                           class="form-input" placeholder="Contoh: SewaJas">
                    @error('app_name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Tagline</label>
                    <input type="text" name="app_tagline"
                           value="{{ old('app_tagline', optional($settings->firstWhere('key', 'app_tagline'))->value) }}"
                           class="form-input" placeholder="Contoh: RENTAL JAS">
                    @error('app_tagline')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ─── PENGATURAN SEWA ──────────────────────────────── --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b" style="border-color:var(--border)">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--secondary)">
                    <i data-lucide="settings" class="w-3.5 h-3.5" style="color:var(--primary)"></i>
                </div>
                <h2 class="font-semibold text-sm" style="color:var(--text-dark)">Pengaturan Sewa</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Denda per Hari Telat (Rp)</label>
                    <input type="number" name="fine_per_day" min="0" step="1"
                           value="{{ old('fine_per_day', optional($settings->firstWhere('key', 'fine_per_day'))->value ?? 0) }}"
                           class="form-input" required>
                    @error('fine_per_day')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color:var(--text-dark)">Durasi Sewa Default (hari)</label>
                    <input type="number" name="rental_duration_days" min="0" step="1"
                           value="{{ old('rental_duration_days', optional($settings->firstWhere('key', 'rental_duration_days'))->value ?? 3) }}"
                           class="form-input" required>
                    @error('rental_duration_days')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Semua
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
function imageUploadForm(initialUrl) {
    return {
        photoPreview: null,
        hasCurrentPhoto: initialUrl ? true : false,
        currentPhotoUrl: initialUrl || '',
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
            this.currentPhotoUrl = '';
            const refs = Object.keys(this.$refs);
            if (refs.length > 0) {
                this.$refs[refs[0]].value = '';
            }
        },
    };
}
</script>
@endpush
@endsection
