@extends('Layouts.app')

@section('title', 'Pencarian - SewaJas')
@section('page-title', 'Pencarian')
@section('subtitle', 'Temukan invoice, customer, dan produk dari satu tempat')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm lg:p-7">
        <form method="GET" action="{{ route('search.index') }}" class="flex flex-col gap-3 lg:flex-row">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" style="margin-top: -1px;"></i>
                <input type="search" name="q" value="{{ $query }}" class="form-input text-base" style="padding-left: 3rem !important; line-height: 1.2;" placeholder="Ketik nomor HP, nama customer, invoice, kode produk...">
            </div>
            <button type="submit" class="btn-primary px-6">
                <i data-lucide="search" class="h-4 w-4"></i>
                Cari
            </button>
        </form>
    </div>

    @if($query === '')
        <div class="rounded-3xl border border-dashed border-slate-300 bg-white/70 p-10 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                <i data-lucide="scan-search" class="h-7 w-7"></i>
            </div>
            <h2 class="text-lg font-extrabold text-slate-950">Mulai dengan nomor HP customer</h2>
            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">Pencarian akan menampilkan customer, invoice terkait, dan produk yang cocok secara bersamaan.</p>
        </div>
    @else
        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-extrabold text-slate-950">Customer</h2>
                    <span class="badge badge-blue">{{ $customers->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($customers as $customer)
                        <a href="{{ route('customers.show', $customer) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/40">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="truncate font-bold text-slate-950">{{ $customer->name }}</div>
                                    <div class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-slate-600">
                                        <i data-lucide="phone" class="h-3.5 w-3.5"></i>
                                        {{ $customer->phone }}
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $customer->rentals_count }} transaksi</div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Tidak ada customer yang cocok.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-extrabold text-slate-950">Invoice</h2>
                    <span class="badge badge-green">{{ $rentals->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($rentals as $rental)
                        <a href="{{ route('rentals.show', $rental) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/40">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate font-mono text-sm font-extrabold text-blue-700">{{ $rental->invoice_number }}</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-800">{{ $rental->customer?->name ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $rental->customer?->phone ?? '' }}</div>
                                </div>
                                <span class="badge badge-yellow">{{ $rental->rental_status }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Tidak ada invoice yang cocok.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-extrabold text-slate-950">Produk</h2>
                    <span class="badge badge-yellow">{{ $products->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse($products as $product)
                        <a href="{{ route('products.show', $product) }}" class="block rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/40">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate font-bold text-slate-950">{{ $product->name }}</div>
                                    <div class="mt-1 text-xs font-semibold text-slate-500">{{ $product->code }} · {{ $product->color ?? '-' }}</div>
                                </div>
                                <span class="badge badge-blue">{{ $product->stock_available ?? 0 }} stok</span>
                            </div>
                        </a>
                    @empty
                        <p class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-500">Tidak ada produk yang cocok.</p>
                    @endforelse
                </div>
            </section>
        </div>
    @endif
</div>
@endsection
