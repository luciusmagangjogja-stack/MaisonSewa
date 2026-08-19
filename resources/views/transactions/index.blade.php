@extends('Layouts.app')

@section('title','Transactions')
@section('page-title','Transactions')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
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
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Status Rental</th>
                        <th>Status Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td class="font-bold">{{ $transaction->invoice_number }}</td>
                        <td>{{ optional($transaction->customer)->name }}</td>
                        <td>{{ $transaction->rental_status }}</td>
                        <td>
                            {{ $transaction->payment_status }}
                            @if($transaction->fine_status === 'unpaid' || $transaction->fine_status === 'partial')
                                <span class="badge badge-yellow text-[10px] ml-1">Denda</span>
                            @endif
                        </td>
                        <td>
                            <a class="action-btn" href="{{ route('transactions.show',$transaction) }}" title="Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-8 text-sm text-slate-500">Tidak ada transaksi.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="mt-4">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection

