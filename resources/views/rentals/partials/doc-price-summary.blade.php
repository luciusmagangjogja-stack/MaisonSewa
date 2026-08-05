@php
    $subtotal = $subtotal ?? 0;
    $discount = $discount ?? 0;
    $deposit = $deposit ?? 0;
    $additionalCharges = $additionalCharges ?? 0;
    $lateFee = $lateFee ?? 0;
    $damageFee = $damageFee ?? 0;
    $totalAmount = $totalAmount ?? 0;
    $paidAmount = $paidAmount ?? 0;
    $changeAmount = $changeAmount ?? 0;
    $remainingBalance = $remainingBalance ?? 0;
    $grandTotal = max($totalAmount, 0);
    $totalPaid = max($paidAmount, 0);
    $change = max($changeAmount, 0);
@endphp

<div class="summary-card">
    <div class="summary-card-title">Payment Summary</div>
    <table class="summary-table" style="width:100%; border-collapse:collapse;">
        <tr>
            <td class="summary-label">Subtotal Rental</td>
            <td class="summary-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($discount > 0)
        <tr>
            <td class="summary-label">Discount</td>
            <td class="summary-value" style="color:#B91C1C;">- Rp {{ number_format($discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($deposit > 0)
        <tr>
            <td class="summary-label">Deposit Dibayar</td>
            <td class="summary-value">Rp {{ number_format($deposit, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($additionalCharges > 0)
        <tr>
            <td class="summary-label">Biaya Tambahan</td>
            <td class="summary-value">Rp {{ number_format($additionalCharges, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($lateFee > 0)
        <tr>
            <td class="summary-label">Denda Telat</td>
            <td class="summary-value" style="color:#B91C1C;">Rp {{ number_format($lateFee, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($damageFee > 0)
        <tr>
            <td class="summary-label">Biaya Kerusakan</td>
            <td class="summary-value" style="color:#B91C1C;">Rp {{ number_format($damageFee, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr style="border-top:1px solid #E5E7EB;">
            <td class="summary-label" style="padding-top:10px; padding-bottom:10px; font-weight:700;">Grand Total</td>
            <td class="summary-value" style="padding-top:10px; padding-bottom:10px; font-weight:800; color:#1E40AF; font-size:24px;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
        @if($totalPaid > 0)
        <tr style="border-top:1px solid #E5E7EB;">
            <td class="summary-label" style="padding-top:8px; color:#15803D; font-weight:700;">Customer Paid</td>
            <td class="summary-value" style="padding-top:8px; color:#15803D; font-weight:700;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
        </tr>
        @if($change > 0)
        <tr>
            <td class="summary-label" style="color:#15803D;">Change</td>
            <td class="summary-value" style="color:#15803D;">Rp {{ number_format($change, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($remainingBalance > 0)
        <tr>
            <td class="summary-label" style="color:#B91C1C; font-weight:700;">Remaining Balance</td>
            <td class="summary-value" style="color:#B91C1C; font-weight:700;">Rp {{ number_format($remainingBalance, 0, ',', '.') }}</td>
        </tr>
        @endif
        @endif
    </table>
</div>
