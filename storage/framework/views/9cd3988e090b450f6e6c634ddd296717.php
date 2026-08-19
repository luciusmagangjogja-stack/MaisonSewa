<?php
    $backUrl = $backUrl ?? null;
    $pdfUrl = $pdfUrl ?? null;
    $printLabel = $printLabel ?? ($printAction ?? 'Cetak');
    $whatsAppUrl = $whatsAppUrl ?? null;
    $copyLinkAction = $copyLinkAction ?? null;
    $copyLinkLabel = $copyLinkLabel ?? 'Salin Tautan';
?>

<div class="no-print doc-toolbar-wrapper">
    <div class="no-print doc-toolbar">
        <?php if(!empty($backUrl)): ?>
            <a class="doc-btn doc-btn-ghost" href="<?php echo e($backUrl); ?>">
                <i data-lucide="chevron-left" class="w-4 h-4"></i> <span class="btn-text">Kembali</span>
            </a>
        <?php endif; ?>

        <?php if(!empty($pdfUrl)): ?>
            <a class="doc-btn doc-btn-primary" href="<?php echo e($pdfUrl); ?>">
                <i data-lucide="download" class="w-4 h-4"></i> <span class="btn-text">Download PDF</span>
            </a>
        <?php endif; ?>

        <?php if(!empty($printLabel)): ?>
            <button class="doc-btn doc-btn-secondary" type="button" onclick="window.print()">
                <i data-lucide="printer" class="w-4 h-4"></i> <span class="btn-text"><?php echo e($printLabel); ?></span>
            </button>
        <?php endif; ?>

        <?php if(!empty($whatsAppUrl)): ?>
            <a class="doc-btn doc-btn-whatsapp" href="<?php echo e($whatsAppUrl); ?>">
                <i data-lucide="message-circle" class="w-4 h-4"></i> <span class="btn-text">WhatsApp</span>
            </a>
        <?php endif; ?>

        <?php if(!empty($copyLinkAction)): ?>
            <button class="doc-btn doc-btn-secondary" type="button" onclick="<?php echo e($copyLinkAction); ?>">
                <i data-lucide="link" class="w-4 h-4"></i> <span class="btn-text"><?php echo e($copyLinkLabel); ?></span>
            </button>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/partials/doc-action-buttons-web.blade.php ENDPATH**/ ?>