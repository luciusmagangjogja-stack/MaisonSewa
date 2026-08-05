<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Rental;

class GenerateTestInvoice extends Command
{
    protected $signature = 'test:invoice {rentalId=181}';
    protected $description = 'Generate test PDF invoice for a given rental ID';

    public function handle(): void
    {
        $rental = Rental::with(['customer', 'branch', 'items', 'createdBy'])->findOrFail($this->argument('rentalId'));
        $pdf = Pdf::loadView('rentals.pdf', compact('rental'));
        $path = storage_path('app/public/invoice_' . $rental->id . '.pdf');
        file_put_contents($path, $pdf->output());
        $this->info("PDF generated: " . $path);
    }
}
