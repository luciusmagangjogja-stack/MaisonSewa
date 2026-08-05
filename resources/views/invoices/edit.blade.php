@extends('Layouts.app')

@section('title', 'Edit Invoice ' . ($invoice->invoice_number ?? ''))
@section('page-title', 'Edit Invoice')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="flex items-center gap-3">
        <a href="{{ route('invoices.show', $invoice) }}" class="btn-secondary p-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold">Edit Invoice {{ $invoice->invoice_number }}</h1>
            <p class="text-sm text-slate-500">Perbarui catatan invoice</p>
        </div>
    </div>
    <div class="card p-5">
        <form method="POST" action="{{ route('invoices.update',$invoice) }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Catatan</label>
                <textarea name="notes" class="form-input" rows="4">{{ $invoice->notes }}</textarea>
            </div>

            <button type="submit" class="btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection

