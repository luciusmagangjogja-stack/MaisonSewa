@php $outputMode='print'; @endphp
@include('rentals.partials.premium-doc-head', ['docTitle'=>'Invoice Print','outputMode'=>$outputMode])
@include('rentals.partials.doc-action-buttons-web', ['backUrl'=>route('invoices.show',$invoice),'pdfUrl'=>route('invoices.pdf',$invoice),'printAction'=>'Print','whatsAppUrl'=>route('invoices.whatsapp',$invoice)])

<div style="padding:20px;">
    <h2 style="font-size:20px; font-weight:700;">Invoice {{ $invoice->invoice_number }}</h2>
    <p>Nama: {{ $invoice->customer?->name }}</p>
    <p>Total: Rp{{ number_format($invoice->total_amount ?? 0,0,',','.') }}</p>
</div>
<script>window.onload=()=>window.print();</script>

