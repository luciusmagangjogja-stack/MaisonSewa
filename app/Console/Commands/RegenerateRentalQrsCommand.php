<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateRentalQrsCommand extends Command
{
    protected $signature = 'rental:regenerate-qrs {--force : overwrite qr_code file even if already exists} {--limit=0 : limit number of rentals (0 = no limit)}';
    protected $description = 'Regenerate all Rental QR codes and update rentals.qr_code to the new scan-result URL format.';

    public function __construct(private RentalService $rentalService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $force = (bool) $this->option('force');

        $query = Rental::query();
        if ($limit > 0) {
            $query->limit($limit);
        }

        $rentals = $query->get();

        $total = $rentals->count();
        $this->info("Found {$total} rental(s) to regenerate QR.");

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rentals as $rental) {
            try {
                $path = $rental->qr_code ? (string) $rental->qr_code : null;

                // If not forcing and existing qr_code file exists, we can skip.
                // However requirement says "recompute all"; still we keep a skip to support --force flag.
                if (!$force && $path) {
                    $fullPath = storage_path('app/public/' . ltrim($path, '/'));
                    if (is_file($fullPath)) {
                        $skipped++;
                        continue;
                    }
                }

                $this->rentalService->generateQrCode($rental);
                $updated++;

                if ($updated % 25 === 0) {
                    $this->info("Progress: {$updated}/{$total} updated");
                }
            } catch (\Throwable $e) {
                $errors++;
                $this->error("Failed rental id={$rental->id} invoice={$rental->invoice_number}: {$e->getMessage()}");
                continue;
            }
        }

        $this->newLine();
        $this->info("Done.");
        $this->table(
            ['metric', 'value'],
            [
                ['total', (string) $total],
                ['updated', (string) $updated],
                ['skipped', (string) $skipped],
                ['errors', (string) $errors],
            ]
        );

        return $errors === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}

