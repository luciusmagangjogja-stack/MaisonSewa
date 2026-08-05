@php
    $brandName = $brandName ?? 'SewaJas';
    $brandTagline = $brandTagline ?? 'Premium Suit Rental';
    $docLabel = $docLabel ?? 'INVOICE';
    $docNumberValue = $docNumberValue ?? ($invoice_number ?? ($rental->invoice_number ?? '-'));
    $docDateValue = $docDateValue ?? null;
    $qrRoute = $qrRoute ?? null;

    $companyAddress = $companyAddress ?? ($rental->branch->address ?? '-');
    $companyPhone = $companyPhone ?? ($rental->branch->phone ?? '-');
    $companyEmail = $companyEmail ?? ($rental->branch->email ?? 'support@sewajas.id');
    $companyWebsite = $companyWebsite ?? 'www.sewajas.id';

    $badgeText = $badgeText ?? ($status ?? 'UNPAID');
    $badgeVariant = $variant ?? 'unpaid';

    $badgeClass = 'status-unpaid';
    if (in_array($badgeVariant, ['paid', 'lunas'], true)) {
        $badgeClass = 'status-paid';
    } elseif (in_array($badgeVariant, ['partial', 'sebagian'], true)) {
        $badgeClass = 'status-partial';
    } elseif ($badgeVariant === 'overdue') {
        $badgeClass = 'status-overdue';
    }

    $logoUrl = null;
    $logoPath = public_path('assets/branding/logo.png');
    if (file_exists($logoPath)) {
        $logoUrl = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }

    $qrBase64 = null;
    if (!empty($qrRoute)) {
        try {
            $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($qrRoute);
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode((string) $qrSvg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('QR generation failed', [
                'route' => $qrRoute,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $qrBase64 = null;
        }
    }
@endphp

<table class="header-table" style="width:100%; border-bottom:2px solid #E5E7EB; padding-bottom:12px; margin-bottom:14px;">
    <tr>
        <!-- KOLOM KIRI: Brand (35%) -->
        <td style="width:35%; vertical-align:top; padding-right:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:54px; padding:0; vertical-align:top;">
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="SewaJas Logo" style="width:54px; height:54px; max-height:54px; object-fit:contain; display:block;">
                        @else
                            <div class="brand-logo">SJ</div>
                        @endif
                    </td>
                    <td style="padding:0; vertical-align:top; padding-left:12px;">
                        <div class="brand-name">{{ $brandName }}</div>
                        <div class="brand-detail">{{ $brandTagline }}</div>
                        @if(!empty($companyAddress))
                            <div class="brand-detail">{{ $companyAddress }}</div>
                        @endif
                        @if(!empty($companyPhone))
                            <div class="brand-detail">Telp: {{ $companyPhone }}</div>
                        @endif
                        @if(!empty($companyEmail))
                            <div class="brand-detail">{{ $companyEmail }}</div>
                        @endif
                        @if(!empty($companyWebsite))
                            <div class="brand-detail">{{ $companyWebsite }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </td>

        <!-- KOLOM TENGAH: Invoice Info (40%) -->
        <td style="width:40%; vertical-align:top; text-align:center; padding:0 10px;">
            <div class="invoice-title">{{ $docLabel }}</div>
            <div style="font-size:15px; font-weight:800; color:#111827; margin-top:2px;">{{ $docNumberValue }}</div>
            <div class="invoice-meta">
                <table style="width:100%; border-collapse:collapse; margin-top:8px;">
                    <tr>
                        <td class="doc-meta-label" style="width:35%; text-align:right; padding-right:6px; vertical-align:top;">Rental #</td>
                        <td class="doc-meta-value" style="text-align:left; vertical-align:top;">#{{ $rental->id ?? '-' }}</td>
                    </tr>
                    @if(!empty($docDateValue))
                    <tr>
                        <td class="doc-meta-label" style="text-align:right; padding-right:6px; vertical-align:top;">Tanggal</td>
                        <td class="doc-meta-value" style="text-align:left; vertical-align:top;">{{ $docDateValue }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </td>

        <!-- KOLOM KANAN: QR Card (25%) -->
        <td style="width:25%; vertical-align:top; text-align:right; padding-left:10px;">
            @if(!empty($qrRoute))
                <div class="qr-card">
                    <img src="{{ $qrBase64 }}" alt="QR Code" class="qr-image" width="100" height="100">
                    <div class="qr-caption">Scan to verify invoice</div>
                </div>
            @endif
        </td>
    </tr>
</table>
