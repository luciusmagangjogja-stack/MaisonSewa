@extends('Layouts.app')

@section('title','Invoices')
@section('page-title','Invoices')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold">Daftar Invoice</h1>
    </div>

    <div class="card p-4">
        <form method="GET" class="flex gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-600">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Invoice / Pelanggan" />
            </div>
            <button type="submit" class="btn-primary">Filter</button>
        </form>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full elegant-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Status Rental</th>
                        <th>Status Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-bold">{{ $invoice->invoice_number }}</td>
                        <td>{{ optional($invoice->customer)->name }}</td>
                        <td>{{ $invoice->rental_status }}</td>
                        <td>{{ $invoice->payment_status }}</td>
                        <td>
                            <a class="action-btn" href="{{ route('invoices.show',$invoice) }}" title="Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-8 text-sm text-slate-500">Tidak ada invoice.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="mt-4">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection

