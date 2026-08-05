@php
    $items = $items ?? collect();
@endphp

<table class="items-table" style="width:100%; border-collapse:collapse; border:1px solid #E5E7EB; border-radius:10px; overflow:hidden;">
    <thead>
        <tr>
            <th style="width:30px; text-align:center; padding:12px 8px;">No</th>
            <th style="width:auto; text-align:left; padding:12px 8px;">Produk</th>
            <th style="width:100px; text-align:left; padding:12px 8px;">Kategori</th>
            <th style="width:60px; text-align:center; padding:12px 8px;">Ukuran</th>
            <th style="width:40px; text-align:center; padding:12px 8px;">Qty</th>
            <th style="width:100px; text-align:right; padding:12px 8px;">Harga/Hari</th>
            <th style="width:50px; text-align:center; padding:12px 8px;">Hari</th>
            <th style="width:110px; text-align:right; padding:12px 8px;">Subtotal</th>
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
            <tr style="background: {{ $index % 2 === 0 ? '#FCFCFD' : '#ffffff' }};">
                <td class="text-center" style="padding:12px 8px;">{{ $index + 1 }}</td>
                <td style="padding:12px 8px;"><strong>{{ $item->product_name ?? '-' }}</strong></td>
                <td style="padding:12px 8px;">{{ $categoryName }}</td>
                <td class="text-center" style="padding:12px 8px;">{{ $size }}</td>
                <td class="text-center" style="padding:12px 8px;">{{ $item->quantity ?? 1 }}</td>
                <td class="text-right" style="padding:12px 8px;">Rp {{ number_format($pricePerDay, 0, ',', '.') }}</td>
                <td class="text-center" style="padding:12px 8px;">{{ $durationDays }}</td>
                <td class="text-right" style="padding:12px 8px; font-weight:700; color:#111827;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
        @if($items->isEmpty())
            <tr>
                <td colspan="8" style="text-align:center; padding:24px; color:#94A3B8;">Tidak ada item</td>
            </tr>
        @endif
    </tbody>
</table>
