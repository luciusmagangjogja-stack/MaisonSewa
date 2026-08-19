<?php
    /**
     * Premium Invoice Web
     * Variables: $rental
     */
    $outputMode = 'web';
?>

<?php echo $__env->make('rentals.partials.premium-doc-head', [
    'docTitle' => 'Invoice ' . ($rental->invoice_number ?? ''),
    'outputMode' => $outputMode,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php echo $__env->make('rentals.partials.doc-action-buttons-web', [
    'backUrl' => route('rentals.show', $rental),
    'pdfUrl' => route('rentals.pdf', $rental),
    'printAction' => 'Print',
    'whatsAppUrl' => route('rentals.whatsapp', $rental),
    'copyLinkAction' => "navigator.clipboard.writeText('" . route('rentals.pdf', $rental) . "'); alert('Link PDF Invoice berhasil disalin!')",

    'copyLinkLabel' => 'Copy Link Invoice',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php echo $__env->make('rentals.partials.doc-parts-invoice', ['rental'=>$rental], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('rentals.partials.doc-close-html', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/invoice.blade.php ENDPATH**/ ?>