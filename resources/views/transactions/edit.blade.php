@extends('Layouts.app')

@section('title', 'Edit Transaction ' . ($transaction->invoice_number ?? ''))
@section('page-title', 'Edit Transaction')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('transactions.show', $transaction) }}" class="btn-secondary p-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold">Edit Transaksi</h1>
            <p class="text-sm text-slate-500">{{ $transaction->invoice_number }}</p>
        </div>
    </div>
    <div class="card p-5">
        <form method="POST" action="{{ route('transactions.update',$transaction) }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Catatan</label>
                <textarea name="notes" class="form-input" rows="4">{{ $transaction->notes }}</textarea>
            </div>

            <button type="submit" class="btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection

