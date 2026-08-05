<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Print</title>
    <style>
        body { font-family: monospace; width: 80mm; margin: auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="text-center">
        <h2>RENTAL JAS</h2>
        <p>{{ optional($rental->branch)->name ?? '-' }}</p>
        <p>{{ $rental->invoice_number }}</p>
    </div>
    <hr>
    <p>Customer: {{ optional($rental->customer)->name ?? '-' }}</p>
    <p>Tgl: {{ optional($rental->rental_date)?->format('d/m/Y') }}</p>
    <hr>
    @foreach($rental->items as $item)
        <p>{{ $item->product_name }} x {{ $item->quantity }}</p>
    @endforeach
    <hr>
    <p class="text-right">TOTAL: Rp {{ number_format(($rental->total_amount ?? 0), 0, ',', '.') }}</p>
</body>
</html>
