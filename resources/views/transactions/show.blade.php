@extends('Layouts.app')

@section('title','Transaction ' . ($transaction->invoice_number ?? ''))
@section('page-title','Transaction')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    <div class="card p-5">
        <div class="flex items-start gap-3">
            <a href="{{ route('transactions.index') }}" class="btn-secondary p-2 mt-0.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold">{{ $transaction->invoice_number }}</h1>
                <div class="text-sm text-slate-600">Pelanggan: {{ $transaction->customer?->name }}</div>
                <div class="text-sm text-slate-600">Cabang: {{ $transaction->branch?->name }}</div>
            </div>
        </div>
    </div>

    <div class="card p-5">
        <h2 class="font-semibold mb-3">Activity Log</h2>
        <div class="space-y-2">
        @forelse($payload['activity_logs'] as $log)
            <div class="rounded-xl border border-slate-200 p-3">
                <div class="font-semibold text-sm">{{ $log->action }}</div>
                <div class="text-xs text-slate-500">{{ $log->created_at?->format('d M Y H:i') }} • {{ $log->user?->name }}</div>
                <div class="text-sm mt-1">{{ $log->description }}</div>
            </div>
        @empty
            <div class="text-sm text-slate-500">Belum ada aktivitas.</div>
        @endforelse
        </div>
    </div>

</div>
@endsection

