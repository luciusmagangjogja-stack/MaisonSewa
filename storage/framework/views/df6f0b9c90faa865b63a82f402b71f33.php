<?php
    $outputMode = 'pdf';
    $receiptPrintedBy = auth()->user()?->name ?? ($rental->createdBy?->name ?? \App\Services\SettingsService::get('app_name', 'SewaJas'));
    $receiptPrintedAt = now()->format('d M Y');
?>

<?php echo $__env->make('rentals.partials.premium-doc-head', [
    'docTitle' => 'Receipt ' . ($receipt['receipt_number'] ?? ''),
    'outputMode' => $outputMode,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('rentals.partials.doc-parts-receipt', ['rental'=>$rental, 'receipt'=>$receipt], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('rentals.partials.doc-close-html', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/receipt_pdf.blade.php ENDPATH**/ ?>