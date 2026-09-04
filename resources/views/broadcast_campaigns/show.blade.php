@extends('Layouts.app')

@section('title', $campaign->name . ' - SewaJas')
@section('page-title', $campaign->name)
@section('subtitle', 'Detail campaign broadcast WhatsApp')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-extrabold text-slate-950">Detail Campaign</h2>
            <p class="mt-1 text-sm text-slate-500">Dibuat {{ $campaign->created_at->format('d M Y H:i') }} oleh {{ $campaign->creator->name ?? '-' }}</p>
        </div>
        <a href="{{ route('broadcast-campaigns.index') }}" class="btn-ghost px-4 py-2">
            <i data-lucide="arrow-left" class="h-4 w-4"></i>
            Kembali
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm lg:p-7">
                <h3 class="mb-4 text-base font-extrabold text-slate-900">Informasi Campaign</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Nama</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $campaign->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Provider</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $campaign->provider }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Tipe Penerima</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ ucfirst($campaign->recipient_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Target Type</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ ucfirst(str_replace('_', ' ', $campaign->target_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ ucfirst($campaign->status) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Scheduled At</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ optional($campaign->scheduled_at)->format('d M Y H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Jeda Antar Pesan</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">
                            @if($campaign->delay_seconds > 0)
                                {{ $campaign->delay_seconds }} detik
                            @else
                                Tanpa jeda
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm lg:p-7">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-slate-900">Log Pengiriman</h3>
                    <span class="badge badge-blue">{{ $campaign->logs->count() }} log</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-2 font-semibold">Penerima</th>
                                <th class="px-4 py-2 font-semibold">Phone</th>
                                <th class="px-4 py-2 font-semibold">Status</th>
                                <th class="px-4 py-2 font-semibold">Pesan</th>
                                <th class="px-4 py-2 font-semibold">Sent At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($campaign->logs as $log)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-2">
                                        <div class="font-medium text-slate-900">{{ $log->recipient?->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500">{{ ucfirst($log->recipient_type) }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-slate-600">{{ $log->phone }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-slate-100 text-slate-700',
                                                'queued' => 'bg-yellow-100 text-yellow-700',
                                                'sent' => 'bg-blue-100 text-blue-700',
                                                'delivered' => 'bg-green-100 text-green-700',
                                                'read' => 'bg-emerald-100 text-emerald-700',
                                                'failed' => 'bg-red-100 text-red-700',
                                            ];
                                            $color = $statusColors[$log->status] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="badge {{ $color }}">{{ ucfirst($log->status) }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-xs text-slate-500 max-w-xs truncate" title="{{ $log->rendered_message }}">
                                        {{ $log->rendered_message }}
                                    </td>
                                    <td class="px-4 py-2 text-xs text-slate-500">{{ optional($log->sent_at)->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">
                                        Belum ada log pengiriman.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm">
                <h3 class="mb-4 text-base font-extrabold text-slate-900">Progress</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Total Target</span>
                        <span class="font-bold text-slate-900">{{ $campaign->total_target }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Berhasil</span>
                        <span class="font-bold text-green-700">{{ $campaign->total_success }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Gagal</span>
                        <span class="font-bold text-red-700">{{ $campaign->total_failed }}</span>
                    </div>
                    <div class="mt-2 h-3 rounded-full bg-slate-100">
                        <div class="h-3 rounded-full bg-blue-600 transition-all" style="width: {{ $campaign->total_target > 0 ? ($campaign->total_success / $campaign->total_target * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            @if($campaign->template)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm">
                    <h3 class="mb-3 text-base font-extrabold text-slate-900">Template</h3>
                    <p class="text-sm font-medium text-slate-900">{{ $campaign->template->name }}</p>
                    <p class="mt-2 text-xs text-slate-500">{{ $campaign->template->content }}</p>
                </div>
            @endif

            @if($campaign->custom_message)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-card-sm">
                    <h3 class="mb-3 text-base font-extrabold text-slate-900">Pesan Custom</h3>
                    @if(is_array($campaign->custom_message) && count($campaign->custom_message) > 1)
                        <div class="space-y-2">
                            @foreach($campaign->custom_message as $index => $msg)
                                <div class="flex items-start gap-2">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-xs font-semibold text-slate-600 flex-shrink-0 mt-0.5">{{ $index + 1 }}</span>
                                    <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $msg }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-700 whitespace-pre-wrap">
                            {{ is_array($campaign->custom_message) ? ($campaign->custom_message[0] ?? '') : $campaign->custom_message }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});
</script>
@endpush
