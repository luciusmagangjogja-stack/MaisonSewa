@extends('Layouts.app')

@section('title', isset($customer) ? 'Edit Customer' : 'Tambah Customer')
@section('page-title', isset($customer) ? 'Edit Customer' : 'Tambah Customer Baru')
@section('subtitle', isset($customer) ? $customer->name : 'Input data pelanggan baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <div class="flex items-center gap-3 mb-5">
<a href="{{ isset($customer) ? route('customers.show', $customer) : route('customers.index') }}" class="btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            {{ isset($customer) ? 'Kembali ke Detail Customer' : 'Kembali ke Customer' }}
        </a>
    </div>

    <form method="POST" action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}"
          enctype="multipart/form-data" x-data="customerForm()">
        @csrf
        @if(isset($customer)) @method('PATCH') @endif

        <!-- Basic Info -->
        <div class="card p-6">
            <h3 class="font-playfair font-semibold text-base mb-5" style="color: var(--text-dark)">
                <i data-lucide="user" class="w-4 h-4 inline mr-2" style="color: var(--primary)"></i>
                Informasi Dasar
            </h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
                           class="form-input" placeholder="Nama lengkap customer" required>
                    @error('name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">Nomor WhatsApp <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium" style="color: var(--text-soft)">+62</span>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                                class="form-input" style="padding-left: 48px !important" placeholder="8123456789" required
                               inputmode="numeric"
                               x-on:input="filterPhoneInput($event)"
                               x-on:keydown="preventNonNumeric($event)">
                    </div>
                    @error('phone')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

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

        <!-- Photos -->
        <div class="card p-6">
            <h3 class="font-playfair font-semibold text-base mb-5" style="color: var(--text-dark)">
                <i data-lucide="camera" class="w-4 h-4 inline mr-2" style="color: var(--primary)"></i>
                Foto Identitas (KTP)
            </h3>
            <div class="max-w-3xl mx-auto">
                <label class="block text-sm font-medium mb-1.5" style="color: var(--text-dark)">Foto Identitas (KTP)</label>
                <div class="relative">
                    <div
                        x-show="!idPhotoPreview && !hasCurrentIdPhoto"
                        class="border-2 border-dashed rounded-xl text-center cursor-pointer transition-all hover:border-amber-400"
                        style="border-color: var(--border); aspect-ratio: 16/10; background: rgba(37,99,235,.03);"
                        @click="$refs.idPhotoInput.click()"
                        @dragover.prevent
                        @dragenter.prevent="isDragging=true"
                        @dragleave.prevent="isDragging=false"
                        @drop.prevent="isDragging=false; onDrop($event)"
                        :class="isDragging ? 'border-blue-500 bg-blue-50' : ''"
                    >
                        <div class="flex flex-col items-center justify-center h-full p-4">
                            <i data-lucide="image-plus" class="w-8 h-8 mx-auto mb-2" style="color: var(--primary)"></i>
                            <p class="text-sm" style="color: var(--text-soft)">Drag & Drop atau Klik untuk upload</p>
                            <p class="text-xs mt-1" style="color: var(--border)">Max 2MB, JPG/PNG (rasio 16:10)</p>
                        </div>
                    </div>

                    <div x-show="idPhotoPreview || hasCurrentIdPhoto" class="relative" style="aspect-ratio: 16/10;">
                        <img :src="idPhotoPreview || currentIdPhotoUrl" class="w-full h-full object-cover rounded-xl">

                        <button type="button" @click="clearIdPhoto()"
                                class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center"
                                style="background: rgba(0,0,0,0.5)">
                            <i data-lucide="x" class="w-3.5 h-3.5 text-white"></i>
                        </button>

                        <button type="button" @click="$refs.idPhotoInput.click()"
                                class="absolute bottom-2 right-2 btn-secondary text-xs px-2 py-1">
                            Replace
                        </button>
                    </div>

                    <input type="file" x-ref="idPhotoInput" name="id_photo" accept="image/*" class="hidden"
                           @change="previewIdPhoto($event)">
                </div>
            </div>
        </div>


        <!-- Submit -->
        <div class="flex items-center justify-between">
            <a href="{{ isset($customer) ? route('customers.show', $customer) : route('customers.index') }}" class="btn-secondary">
                <i data-lucide="x" class="w-4 h-4"></i>
                Batal
            </a>
            <button type="submit" class="btn-primary px-8">
                <i data-lucide="{{ isset($customer) ? 'save' : 'user-plus' }}" class="w-4 h-4"></i>
                {{ isset($customer) ? 'Simpan Perubahan' : 'Tambah Customer' }}
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function customerForm() {
    return {
        idPhotoPreview: null,
        hasCurrentIdPhoto: {{ isset($customer) && $customer->id_photo ? 'true' : 'false' }},
        currentIdPhotoUrl: '{{ isset($customer) && $customer->id_photo ? asset("storage/".$customer->id_photo) : "" }}',

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
@endpush
