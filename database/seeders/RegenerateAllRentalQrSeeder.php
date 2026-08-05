<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Services\RentalService;
use Illuminate\Support\Facades\Log;

class RegenerateAllRentalQrSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(RentalService::class);

        $rentals = Rental::query()->get();
        $count = $rentals->count();

        $updated = 0;
        $errors = 0;

        foreach ($rentals as $rental) {
            try {
                $service->generateQrCode($rental);
                $updated++;

                if ($updated % 50 === 0) {
                    $this->command?->info("Progress: {$updated}/{$count}");
                }
            } catch (\Throwable $e) {
                $errors++;
                $msg = "Regenerate QR failed rental_id={$rental->id} invoice={$rental->invoice_number}: {$e->getMessage()}";
                $this->command?->error($msg);
                Log::error($msg, ['exception' => $e]);
            }
        }

        $this->command?->info("Done. Total rentals={$count}, updated={$updated}, errors={$errors}");
    }
}

