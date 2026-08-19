<?php
    $outputMode = 'web';
?>

<?php echo $__env->make('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt',
    'outputMode' => $outputMode,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="no-print doc-toolbar-wrapper">
    <div class="no-print doc-toolbar">
        <a class="doc-btn doc-btn-ghost" href="<?php echo e(route('rentals.show', $rental)); ?>">
            <i data-lucide="chevron-left" class="w-4 h-4"></i> <span class="btn-text">Kembali</span>
        </a>
        <a class="doc-btn doc-btn-primary" href="<?php echo e(route('rentals.receipt.pdf', $rental)); ?>">
            <i data-lucide="download" class="w-4 h-4"></i> <span class="btn-text">Download PDF</span>
        </a>
        <button class="doc-btn doc-btn-secondary" type="button" onclick="window.print()">
            <i data-lucide="printer" class="w-4 h-4"></i> <span class="btn-text">Print</span>
        </button>
        <a class="doc-btn doc-btn-whatsapp" href="<?php echo e(route('rentals.receipt.whatsapp', $rental)); ?>">
            <i data-lucide="message-circle" class="w-4 h-4"></i> <span class="btn-text">WhatsApp</span>
        </a>
        <button class="doc-btn doc-btn-secondary" type="button" onclick="navigator.clipboard.writeText('<?php echo e(route('rentals.receipt.pdf', $rental)); ?>'); alert('Link PDF Receipt berhasil disalin!')">
            <i data-lucide="link" class="w-4 h-4"></i> <span class="btn-text">Copy Link Receipt</span>
        </button>
    </div>
</div>

<?php echo $__env->make('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('rentals.partials.doc-close-html', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/receipt.blade.php ENDPATH**/ ?>