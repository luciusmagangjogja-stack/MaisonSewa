<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillFineStatusCommand extends Command
{
    protected $signature = 'rentals:backfill-fine-status 
                            {--dry-run : Preview changes without writing to database}
                            {--force : Actually apply changes to database}';
    protected $description = 'Backfill fine_status, fine_amount, and fine_paid_amount for returned rentals';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        if (!$isDryRun && !$isForce) {
            $this->warn('Please specify --dry-run to preview or --force to apply changes.');
            return 1;
        }

        $rentals = Rental::where('rental_status', 'returned')
            ->where('fine_status', 'none')
            ->with(['items', 'payments'])
            ->get();

        if ($rentals->isEmpty()) {
            $this->info('No returned rentals with fine_status = none found.');
            return 0;
        }

        $rows = [];
        foreach ($rentals as $rental) {
            $damageFee = $rental->items->sum('damage_fee');
            $lateFee = (float) ($rental->late_fee ?? 0);
            $fineAmount = $damageFee + $lateFee;

            $finePaidAmount = $rental->payments()
                ->whereIn('type', ['late_fee', 'damage_fee'])
                ->sum('amount');

            if ($fineAmount <= 0) {
                $fineStatus = Rental::FINE_NONE;
            } elseif ($finePaidAmount <= 0) {
                $fineStatus = Rental::FINE_UNPAID;
            } elseif ($finePaidAmount < $fineAmount) {
                $fineStatus = Rental::FINE_PARTIAL;
            } else {
                $fineStatus = Rental::FINE_PAID;
            }

            $rows[] = [
                'ID' => $rental->id,
                'Invoice' => $rental->invoice_number,
                'Late Fee' => number_format($lateFee, 0, ',', '.'),
                'Damage Fee' => number_format($damageFee, 0, ',', '.'),
                'Fine Amount' => number_format($fineAmount, 0, ',', '.'),
                'Fine Paid' => number_format($finePaidAmount, 0, ',', '.'),
                'Fine Status' => $fineStatus,
            ];
        }

        $this->table(
            ['ID', 'Invoice', 'Late Fee', 'Damage Fee', 'Fine Amount', 'Fine Paid', 'Fine Status'],
            $rows
        );

        if ($isForce) {
            $confirmed = $this->confirm('Apply these changes to the database?');
            if (!$confirmed) {
                $this->info('Aborted.');
                return 0;
            }

            $bar = $this->output->createProgressBar(count($rentals));
            $bar->start();

            foreach ($rentals as $rental) {
                $damageFee = $rental->items->sum('damage_fee');
                $lateFee = (float) ($rental->late_fee ?? 0);
                $fineAmount = $damageFee + $lateFee;

                $finePaidAmount = $rental->payments()
                    ->whereIn('type', ['late_fee', 'damage_fee'])
                    ->sum('amount');

                if ($fineAmount <= 0) {
                    $fineStatus = Rental::FINE_NONE;
                } elseif ($finePaidAmount <= 0) {
                    $fineStatus = Rental::FINE_UNPAID;
                } elseif ($finePaidAmount < $fineAmount) {
                    $fineStatus = Rental::FINE_PARTIAL;
                } else {
                    $fineStatus = Rental::FINE_PAID;
                }

                $rental->update([
                    'fine_amount' => $fineAmount,
                    'fine_paid_amount' => $finePaidAmount,
                    'fine_status' => $fineStatus,
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Backfill completed. {$rentals->count()} rentals updated.");
        } else {
            $this->info('This was a dry run. No changes were made to the database.');
            $this->info('Run with --force to apply changes.');
        }

        return 0;
    }
}
