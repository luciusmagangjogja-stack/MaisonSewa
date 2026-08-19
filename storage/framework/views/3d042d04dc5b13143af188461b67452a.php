<?php
    $outputMode = 'pdf';
?>

<?php echo $__env->make('rentals.partials.premium-doc-head', [
    'docTitle' => 'Invoice ' . ($rental->invoice_number ?? ''),
    'outputMode' => $outputMode,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php echo $__env->make('rentals.partials.doc-parts-invoice', ['rental'=>$rental], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('rentals.partials.doc-close-html', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/pdf.blade.php ENDPATH**/ ?>