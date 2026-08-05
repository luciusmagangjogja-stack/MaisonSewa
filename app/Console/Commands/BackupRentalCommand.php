<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Models\Payment;
use App\Models\RentalItem;
use App\Models\RentalReturn;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupRentalCommand extends Command
{
    protected $signature = 'backup:rental {invoice : Rental invoice_number} {--path=storage/backups/ : Output directory (within project)}';
    protected $description = 'Backup rental data (rentals + rental_items + payments + rental_returns) into JSON file.';

    public function handle(): int
    {
        $invoice = (string) $this->argument('invoice');
        $basePath = (string) $this->option('path');

        if ($basePath === '') {
            $basePath = 'storage/backups/';
        }
        $basePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR;

        $rental = Rental::query()
            ->with(['items', 'payments', 'returnRecord', 'createdBy', 'branch', 'customer'])
            ->where('invoice_number', $invoice)
            ->first();

        if (!$rental) {
            $this->error("Rental not found for invoice: {$invoice}");
            return 1;
        }

        $timestamp = now()->format('Ymd_His');
        $fileName = $rental->invoice_number . '_backup_' . $timestamp . '.json';
        $fullPath = base_path($basePath . $fileName);

        $items = $rental->items()->get();
        $payments = $rental->payments()->get();
        $returns = $rental->returnRecord()->get();

        $payload = [
            'meta' => [
                'invoice_number' => $rental->invoice_number,
                'branch_id' => $rental->branch_id,
                'created_by' => $rental->created_by,
                'backed_up_at' => now()->toIso8601String(),
                'tables' => [
                    'rentals' => 1,
                    'rental_items' => $items->count(),
                    'payments' => $payments->count(),
                    'rental_returns' => $returns->count(),
                ],
            ],
            'rentals' => $rental,
            'rental_items' => $items,
            'payments' => $payments,
            'rental_returns' => $returns,
        ];

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $json = json_encode($payload, JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->error('Failed to encode backup payload to JSON.');
            return 1;
        }

        file_put_contents($fullPath, $json);

        $this->info('Backup selesai.');
        $this->line('File: ' . $fullPath);
        $this->line('Counts:');
        $this->line(' - rental: 1');
        $this->line(' - rental_items: ' . $items->count());
        $this->line(' - payments: ' . $payments->count());
        $this->line(' - rental_returns: ' . $returns->count());

        return 0;
    }
}

