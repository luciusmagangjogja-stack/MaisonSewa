@extends('Layouts.app')

@section('title', 'Invoice ' . ($invoice->invoice_number ?? ''))
@section('page-title', 'Invoice')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    <div class="card p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <a href="{{ route('invoices.index') }}" class="btn-secondary p-2 mt-0.5">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
                    <div class="text-sm text-slate-600">Pelanggan: {{ $invoice->customer?->name }} ({{ $invoice->customer?->phone }})</div>
                    <div class="text-sm text-slate-600">Cabang: {{ $invoice->branch?->name }}</div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('invoices.index') }}" class="btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Invoice
                </a>
                <a class="doc-btn doc-btn-secondary" href="{{ route('invoices.print',$invoice) }}">Print</a>
                <a class="doc-btn doc-btn-primary" href="{{ route('invoices.pdf',$invoice) }}">Download PDF</a>
                <a class="doc-btn" href="{{ route('invoices.whatsapp',$invoice) }}" style="border-color: rgba(201,168,76,.35); background: rgba(201,168,76,.18); color: var(--brown-900);">WhatsApp</a>
                @if(auth()->user()->isSuperAdmin() && in_array($invoice->rental_status, ['waiting', 'active', 'overdue']))
                <button type="button" onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="doc-btn" style="border-color: #f59e0b; color: #f59e0b; background: #fffbeb;">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i> Batalkan
                </button>
                @endif
                @if(auth()->user()->isSuperAdmin())
                <button type="button" onclick="document.getElementById('deleteInvoiceModal').classList.remove('hidden')" class="doc-btn" style="border-color: #dc2626; color: #dc2626; background: #fef2f2;">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Hapus
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2 space-y-4">
            <div class="card p-5">
                <div class="font-semibold mb-3">Items</div>
                <div class="overflow-x-auto">
                    <table class="w-full elegant-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Harga/Hari</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->product_name ?? $item->product?->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp{{ number_format($item->price_per_day ?? 0,0,',','.') }}</td>
                                <td>Rp{{ number_format($item->subtotal ?? 0,0,',','.') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-5">
                <div class="font-semibold mb-3">Ringkasan</div>
                <div class="text-sm flex justify-between"><span>Subtotal</span><b>Rp{{ number_format($invoice->subtotal ?? 0,0,',','.') }}</b></div>
                <div class="text-sm flex justify-between mt-2"><span>Diskon</span><b>Rp{{ number_format($invoice->discount ?? 0,0,',','.') }}</b></div>
                <div class="text-sm flex justify-between mt-2"><span>Denda</span><b>Rp{{ number_format($invoice->late_fee ?? 0,0,',','.') }}</b></div>
                <div class="border-t my-3"></div>
                <div class="text-base flex justify-between"><span>Total</span><b>Rp{{ number_format($invoice->total_amount ?? 0,0,',','.') }}</b></div>
                <div class="text-sm mt-2">Paid: Rp{{ number_format($invoice->paid_amount ?? 0,0,',','.') }}</div>
                <div class="text-sm">Remaining: Rp{{ number_format($invoice->remaining_amount ?? 0,0,',','.') }}</div>
            </div>

            <div class="card p-5">
                <h2 class="font-semibold mb-3">Pembayaran</h2>
                <div class="grid grid-cols-1 gap-2">
                    @forelse($invoice->payments as $payment)
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3">
                            <div>
                                <div class="font-semibold">{{ $payment->payment_number }} <span class="text-xs text-slate-500">({{ $payment->type ?? 'payment' }})</span></div>
                                <div class="text-xs text-slate-500">{{ $payment->paid_at?->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">Rp{{ number_format((float)$payment->amount,0,',','.') }}</div>
                                <div class="text-xs text-slate-500">{{ $payment->method_label ?? $payment->method }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Batalkan Invoice</h3>
            <p class="text-sm text-slate-600 mb-6">
                Apakah Anda yakin ingin membatalkan invoice ini?<br>
                Semua stok barang akan dikembalikan.<br>
                Invoice akan tetap tersimpan sebagai arsip.<br>
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 ds-transition">Batal</button>
                <form method="POST" action="{{ route('invoices.cancel', $invoice) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-yellow-600 hover:bg-yellow-700 ds-transition">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Invoice Modal -->
    <div id="deleteInvoiceModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Hapus Invoice</h3>
            <p class="text-sm text-slate-600 mb-6">
                Apakah Anda yakin ingin menghapus invoice ini?<br>
                Semua data rental yang terkait akan ikut dihapus.<br>
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('deleteInvoiceModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 ds-transition">Batal</button>
                <form method="POST" action="{{ route('rentals.destroy', $invoice) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 ds-transition">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

