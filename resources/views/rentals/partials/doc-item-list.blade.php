@php
    $items = $items ?? collect();
@endphp

<table class="items-table" style="width:100%; border-collapse:collapse; border:1px solid #E5E7EB; border-radius:10px; overflow:hidden; font-size:10px;">
    <thead>
        <tr>
            <th style="width:24px; text-align:center; padding:8px 6px;">No</th>
            <th style="width:auto; text-align:left; padding:8px 6px;">Produk</th>
            <th style="width:80px; text-align:left; padding:8px 6px;">Kategori</th>
            <th style="width:48px; text-align:center; padding:8px 6px;">Ukuran</th>
            <th style="width:32px; text-align:center; padding:8px 6px;">Qty</th>
            <th style="width:80px; text-align:right; padding:8px 6px;">Harga/Hari</th>
            <th style="width:40px; text-align:center; padding:8px 6px;">Hari</th>
            <th style="width:90px; text-align:right; padding:8px 6px;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $index => $item)
            @php
                $categoryName = $item->product?->category?->name ?? '-';
                $size = $item->size ?? '-';
                $pricePerDay = $item->price_per_day ?? 0;
                $durationDays = $item->duration_days ?? 0;
                $subtotal = $item->subtotal ?? 0;
            @endphp
            <tr style="background: {{ $index % 2 === 0 ? '#FCFCFD' : '#ffffff' }}; font-size:10px;">
                <td class="text-center" style="padding:8px 6px;">{{ $index + 1 }}</td>
                <td style="padding:8px 6px;"><strong>{{ $item->product_name ?? '-' }}</strong></td>
                <td style="padding:8px 6px;">{{ $categoryName }}</td>
                <td class="text-center" style="padding:8px 6px;">{{ $size }}</td>
                <td class="text-center" style="padding:8px 6px;">{{ $item->quantity ?? 1 }}</td>
                <td class="text-right" style="padding:8px 6px;">Rp {{ number_format($pricePerDay, 0, ',', '.') }}</td>
                <td class="text-center" style="padding:8px 6px;">{{ $durationDays }}</td>
                <td class="text-right" style="padding:8px 6px; font-weight:700; color:#111827;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        @if($items->isEmpty())
            <tr>
                <td colspan="8" style="text-align:center; padding:20px; color:#94A3B8; font-size:10px;">Tidak ada item</td>
            </tr>
        @endif
    </tbody>
</table>
