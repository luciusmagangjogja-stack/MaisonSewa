<?php
    $brandName = $brandName ?? \App\Services\SettingsService::get('app_name', 'SewaJas');
    $brandTagline = $brandTagline ?? \App\Services\SettingsService::get('company_tagline', 'Premium Suit Rental');
    $docLabel = $docLabel ?? 'INVOICE';
    $docNumberValue = $docNumberValue ?? ($invoice_number ?? ($rental->invoice_number ?? '-'));
    $docDateValue = $docDateValue ?? null;
    $qrRoute = $qrRoute ?? null;

    $companyAddress = $companyAddress ?? (\App\Services\SettingsService::get('company_address') ?: ($rental->branch->address ?? '-'));
    $companyPhone = $companyPhone ?? (\App\Services\SettingsService::get('company_phone') ?: ($rental->branch->phone ?? '-'));
    $companyEmail = $companyEmail ?? \App\Services\SettingsService::get('company_email', 'support@sewajas.id');
    $companyWebsite = $companyWebsite ?? \App\Services\SettingsService::get('company_website', 'www.sewajas.id');

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
    $logoPath = \App\Services\SettingsService::get('app_logo');
    if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
        $fullPath = storage_path('app/public/' . $logoPath);
        if (file_exists($fullPath)) {
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
            $logoUrl = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($fullPath));
        }
    }

    $qrBase64 = null;
    if (!empty($qrRoute)) {
        try {
            $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(100)->generate($qrRoute);
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
?>

<table class="header-table" style="width:100%; border-bottom:2px solid #E5E7EB; padding-bottom:12px; margin-bottom:14px;">
    <tr>
        <!-- KOLOM KIRI: Brand (35%) -->
        <td style="width:35%; vertical-align:top; padding-right:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:54px; padding:0; vertical-align:top;">
                        <?php if(!empty($logoUrl)): ?>
                            <img src="<?php echo e($logoUrl); ?>" alt="SewaJas Logo" style="width:54px; height:54px; max-height:54px; object-fit:contain; display:block;">
                        <?php else: ?>
                            <div class="brand-logo">SJ</div>
                        <?php endif; ?>
                    </td>
                    <td style="padding:0; vertical-align:top; padding-left:12px;">
                        <div class="brand-name"><?php echo e($brandName); ?></div>
                        <div class="brand-detail"><?php echo e($brandTagline); ?></div>
                        <?php if(!empty($companyAddress)): ?>
                            <div class="brand-detail"><?php echo e($companyAddress); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($companyPhone)): ?>
                            <div class="brand-detail">Telp: <?php echo e($companyPhone); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($companyEmail)): ?>
                            <div class="brand-detail"><?php echo e($companyEmail); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($companyWebsite)): ?>
                            <div class="brand-detail"><?php echo e($companyWebsite); ?></div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>

        <!-- KOLOM TENGAH: Invoice Info (40%) -->
        <td style="width:40%; vertical-align:top; text-align:center; padding:0 10px;">
            <div class="invoice-title"><?php echo e($docLabel); ?></div>
            <div style="font-size:15px; font-weight:800; color:#111827; margin-top:2px;"><?php echo e($docNumberValue); ?></div>
            <div class="invoice-meta">
                <table style="width:100%; border-collapse:collapse; margin-top:8px;">
                    <tr>
                        <td class="doc-meta-label" style="width:35%; text-align:right; padding-right:6px; vertical-align:top;">Rental #</td>
                        <td class="doc-meta-value" style="text-align:left; vertical-align:top;">#<?php echo e($rental->id ?? '-'); ?></td>
                    </tr>
                    <?php if(!empty($docDateValue)): ?>
                    <tr>
                        <td class="doc-meta-label" style="text-align:right; padding-right:6px; vertical-align:top;">Tanggal</td>
                        <td class="doc-meta-value" style="text-align:left; vertical-align:top;"><?php echo e($docDateValue); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </td>

        <!-- KOLOM KANAN: QR Card (25%) -->
        <td style="width:25%; vertical-align:top; text-align:right; padding-left:10px;">
            <?php if(!empty($qrRoute)): ?>
                <div class="qr-card">
                    <img src="<?php echo e($qrBase64); ?>" alt="QR Code" class="qr-image" width="100" height="100">
                    <div class="qr-caption">Scan to verify invoice</div>
                </div>
            <?php endif; ?>
        </td>
    </tr>
</table>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-brand-header.blade.php ENDPATH**/ ?>