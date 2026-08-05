@extends('Layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">Pengaturan</h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">Konfigurasi denda dan durasi sewa default</p>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @include('components.flash-messages')

    {{-- FORM --}}
    <div class="card">
        <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
            @csrf

            @php
                $finePerDay = optional($settings->firstWhere('key', 'fine_per_day'))->value;
                $durationDays = optional($settings->firstWhere('key', 'rental_duration_days'))->value;
            @endphp

            <div>
                <label class="block text-sm font-medium" style="color: var(--text-soft)">Denda per Hari Telat (Rp)</label>
                <div class="mt-1">
                    <input
                        type="number"
                        name="fine_per_day"
                        min="0"
                        step="1"
                        value="{{ $finePerDay ?? 0 }}"
                        class="form-input"
                        required
                    >
                </div>
                @error('fine_per_day')
                    <p class="text-sm mt-1" style="color: #DC2626">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium" style="color: var(--text-soft)">Durasi Sewa Default (hari)</label>
                <div class="mt-1">
                    <input
                        type="number"
                        name="rental_duration_days"
                        min="0"
                        step="1"
                        value="{{ $durationDays ?? 3 }}"
                        class="form-input"
                        required
                    >
                </div>
                @error('rental_duration_days')
                    <p class="text-sm mt-1" style="color: #DC2626">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

