{{-- resources/views/commissions/index.blade.php --}}
@extends('Layouts.app')

@section('title', 'Laporan Komisi')

@section('content')
<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="font-playfair text-2xl font-semibold" style="color: var(--text-dark)">
                @if (auth()->user()->isSales())
                    Komisi Saya
                @else
                    Laporan Komisi
                @endif
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--text-soft)">
                @if (auth()->user()->isSales())
                    Cabang: {{ auth()->user()->branch?->name ?? '-' }}
                @else
                    @if ($branchId)
                        Cabang: <span class="font-semibold" style="color: var(--text-dark)">{{ $branches->firstWhere('id', $branchId)?->name }}</span>
                    @else
                        Menampilkan data <span class="font-semibold" style="color: var(--text-dark)">semua cabang</span>
                    @endif
                @endif
            </p>
        </div>
    </div>

    {{-- FILTER BAR --}}
    @if (!auth()->user()->isSales())
    <div class="card mb-6 overflow-hidden">
        <form method="GET" action="{{ url()->current() }}">
            <div class="flex flex-wrap items-end gap-4 p-5">
                <div class="flex flex-col gap-1.5 min-w-[200px]">
                    <label class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-soft)">Sales</label>
                    <div class="relative">
                        <select name="sales_id" class="form-input pr-8 appearance-none cursor-pointer">
                            <option value="">— Semua Sales —</option>
                            @foreach ($salesList as $sales)
                                <option value="{{ $sales->id }}" {{ request('sales_id') == $sales->id ? 'selected' : '' }}>
                                    {{ $sales->name }} ({{ $sales->branch?->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color: var(--text-soft)"></i>
                    </div>
                </div>

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
                    <label for="start_date" class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-soft)">Dari</label>
                    <input type="date" name="start_date" id="start_date" class="form-input" value="{{ request('start_date') }}">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="end_date" class="text-xs font-semibold uppercase tracking-wider" style="color: var(--text-soft)">Sampai</label>
                    <input type="date" name="end_date" id="end_date" class="form-input" value="{{ request('end_date') }}">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-semibold uppercase tracking-wider opacity-0">—</label>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        Tampilkan
                    </button>
                </div>

                @if (request()->hasAny(['start_date', 'end_date', 'sales_id', 'branch_id']))
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                        Total Komisi
                    </p>
                    <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                        Rp {{ number_format($totalCommission, 0, ',', '.') }}
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
                        Jumlah Transaksi
                    </p>
                    <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                        {{ $totalTransactions }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: #DBEAFE">
                    <i data-lucide="receipt" class="w-5 h-5" style="color: #2563EB"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--text-soft)">
                        Rata-rata Komisi
                    </p>
                    <p class="font-playfair text-2xl font-bold" style="color: var(--text-dark)">
                        Rp {{ number_format($totalTransactions > 0 ? $totalCommission / $totalTransactions : 0, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: #D1FAE5">
                    <i data-lucide="trending-up" class="w-5 h-5" style="color: #059669"></i>
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
                        <th class="text-left">Invoice</th>
                        <th class="text-center">Tanggal</th>
                        @if (!auth()->user()->isSales())
                        <th class="text-left">Sales</th>
                        @endif
                        <th class="text-left">Customer</th>
                        <th class="text-right">Nilai Sewa</th>
                        <th class="text-right">Diskon</th>
                        <th class="text-center">Rate</th>
                        <th class="text-right">Komisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rentals as $rental)
                        @php
                            $commissionable = max(0, $rental->subtotal - $rental->discount);
                            $rate = $rental->createdBy && $rental->createdBy->isSales() ? $rental->createdBy->commission_rate : 0;
                        @endphp
                        <tr>
                            <td class="font-semibold" style="color: var(--text-dark)">{{ $rental->invoice_number }}</td>
                            <td class="text-center text-sm" style="color: var(--text-soft)">{{ $rental->rental_date?->format('d M Y') ?? '-' }}</td>
                            @if (!auth()->user()->isSales())
                            <td class="text-sm" style="color: var(--text-dark)">{{ $rental->createdBy->name ?? '-' }}</td>
                            @endif
                            <td class="text-sm" style="color: var(--text-dark)">{{ $rental->customer->name ?? '-' }}</td>
                            <td class="text-right text-sm" style="color: var(--text-dark)">Rp {{ number_format($rental->subtotal, 0, ',', '.') }}</td>
                            <td class="text-right text-sm" style="color: var(--text-soft)">Rp {{ number_format($rental->discount, 0, ',', '.') }}</td>
                            <td class="text-center text-sm" style="color: var(--text-dark)">{{ $rate }}%</td>
                            <td class="text-right text-sm font-semibold" style="color: #059669">Rp {{ number_format($rental->commission_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSales() ? 7 : 8 }}" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: var(--secondary)">
                                        <i data-lucide="wallet" class="w-5 h-5" style="color: var(--text-soft)"></i>
                                    </div>
                                    <p class="text-sm font-medium" style="color: var(--text-dark)">Belum ada data komisi</p>
                                    <p class="text-xs" style="color: var(--text-soft)">Komisi akan muncul setelah transaksi selesai (returned + lunas)</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($rentals->hasPages())
            <div class="px-5 py-4 border-t" style="border-color: var(--border)">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
