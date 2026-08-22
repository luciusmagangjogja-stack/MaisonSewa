{{-- resources/views/points/index.blade.php --}}
@extends('Layouts.app')

@section('title', 'Poin Saya')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-semibold" style="color: var(--text-dark)">
                @if (auth()->user()->isSales())
                    Poin Saya
                @else
                    Laporan Poin
                @endif
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">
                @if (auth()->user()->isSales())
                    Cabang: {{ auth()->user()->branch?->name ?? '-' }}
                @else
                    @if ($branches->count() > 1)
                        @if (request('branch_id'))
                            Cabang: <span class="font-semibold" style="color: var(--text-dark)">{{ $branches->firstWhere('id', request('branch_id'))?->name }}</span>
                        @else
                            Menampilkan data <span class="font-semibold" style="color: var(--text-dark)">semua cabang</span>
                        @endif
                    @else
                        Cabang: <span class="font-semibold" style="color: var(--text-dark)">{{ $branches->first()?->name ?? '-' }}</span>
                    @endif
                @endif
            </p>
        </div>
    </div>

    {{-- FILTER BAR (admin only) --}}
    @if (!auth()->user()->isSales() && $branches->count() > 1)
    <div class="card mb-6 overflow-hidden">
        <form method="GET" action="{{ url()->current() }}">
            <div class="flex flex-wrap items-end gap-4 p-5">
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-soft)">Cabang</label>
                    <div class="relative">
                        <select name="branch_id" class="form-input pr-8 appearance-none cursor-pointer">
                            <option value="">— Semua Cabang —</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ (int) request('branch_id') === $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color: var(--text-soft)"></i>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider opacity-0">—</label>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Tampilkan
                    </button>
                </div>

                @if (request()->has('branch_id'))
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold uppercase tracking-wider opacity-0">—</label>
                        <a href="{{ url()->current() }}" class="btn-secondary">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Reset
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                        Total Poin
                    </p>
                    <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                        {{ number_format($totalPoints, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: var(--gold-light)">
                    <i data-lucide="wallet" class="w-5 h-5" style="color: var(--gold)"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                        Jumlah Sales
                    </p>
                    <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                        {{ $salesList->total() }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: #DBEAFE">
                    <i data-lucide="users" class="w-5 h-5" style="color: #2563EB"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="elegant-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Sales</th>
                        <th class="text-left">Cabang</th>
                        <th class="text-right">Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salesList as $sales)
                        <tr>
                            <td class="font-semibold" style="color: var(--text-dark)">{{ $sales->name }}</td>
                            <td class="text-sm" style="color: var(--text-soft)">{{ $sales->branch?->name ?? '-' }}</td>
                            <td class="text-right text-sm font-semibold" style="color: var(--text-dark)">
                                {{ number_format($sales->total_points, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: var(--secondary)">
                                        <i data-lucide="wallet" class="w-5 h-5" style="color: var(--text-soft)"></i>
                                    </div>
                                    <p class="text-sm font-medium" style="color: var(--text-dark)">Belum ada poin</p>
                                    <p class="text-xs" style="color: var(--text-soft)">Poin akan muncul setelah transaksi penyewaan dan pengembalian selesai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($salesList->hasPages())
            <div class="px-5 py-4 border-t" style="border-color: var(--border)">
                {{ $salesList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
