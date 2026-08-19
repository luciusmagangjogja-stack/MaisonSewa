<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculatePaymentStatusCommand extends Command
{
    protected $signature = 'rentals:recalculate-payment-status
                            {--dry-run : Preview changes without writing to database}
                            {--force : Actually apply changes to database}';
    protected $description = 'Recalculate paid_amount, fine_paid_amount, payment_status, and fine_status from actual payments';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        if (!$isDryRun && !$isForce) {
            $this->warn('Please specify --dry-run to preview or --force to apply changes.');
            return 1;
        }

        $rentals = Rental::with('payments')->get();

        if ($rentals->isEmpty()) {
            $this->info('No rentals found.');
            return 0;
        }

        $rows = [];
        $updateCount = 0;

        foreach ($rentals as $rental) {
            $rentalFee = (float) $rental->total_amount;
            $fineAmount = (float) $rental->fine_amount;

            $totalPaidForRental = (float) $rental->payments()
                ->whereIn('type', ['rental', 'deposit'])
                ->where('type', '!=', 'refund')
                ->sum('amount');

            $totalPaidForFine = (float) $rental->payments()
                ->whereIn('type', ['late_fee', 'damage_fee'])
                ->where('type', '!=', 'refund')
                ->sum('amount');

            $remainingRental = max(0, $rentalFee - $totalPaidForRental);
            $remainingFine = max(0, $fineAmount - $totalPaidForFine);

            if ($totalPaidForRental <= 0) {
                $paymentStatus = Rental::PAYMENT_UNPAID;
            } elseif ($totalPaidForRental < $rentalFee) {
                $paymentStatus = Rental::PAYMENT_PARTIAL;
            } else {
                $paymentStatus = Rental::PAYMENT_PAID;
            }

            if ($fineAmount <= 0) {
                $fineStatus = Rental::FINE_NONE;
            } elseif ($totalPaidForFine <= 0) {
                $fineStatus = Rental::FINE_UNPAID;
            } elseif ($totalPaidForFine < $fineAmount) {
                $fineStatus = Rental::FINE_PARTIAL;
            } else {
                $fineStatus = Rental::FINE_PAID;
            }

            $totalOwed = max(0, $rentalFee + $fineAmount);
            $totalPaidRaw = (float) $rental->payments()
                ->where('type', '!=', 'refund')
                ->sum('amount');
            $refundGiven = (float) abs($rental->payments()
                ->where('type', 'refund')
                ->sum('amount'));
            $overpayment = max(0, $totalPaidRaw - $totalOwed);
            $changeAmount = max(0, $overpayment - $refundGiven);

            $needsUpdate = false;
            $changes = [];

            if ((float) $rental->paid_amount !== $totalPaidForRental) {
                $needsUpdate = true;
                $changes[] = "paid_amount: {$rental->paid_amount} → {$totalPaidForRental}";
            }
            if ((float) $rental->fine_paid_amount !== $totalPaidForFine) {
                $needsUpdate = true;
                $changes[] = "fine_paid_amount: {$rental->fine_paid_amount} → {$totalPaidForFine}";
            }
            if ($rental->payment_status !== $paymentStatus) {
                $needsUpdate = true;
                $changes[] = "payment_status: {$rental->payment_status} → {$paymentStatus}";
            }
            if ($rental->fine_status !== $fineStatus) {
                $needsUpdate = true;
                $changes[] = "fine_status: {$rental->fine_status} → {$fineStatus}";
            }
            if ((float) $rental->change_amount !== $changeAmount) {
                $needsUpdate = true;
                $changes[] = "change_amount: {$rental->change_amount} → {$changeAmount}";
            }

            if (!$needsUpdate) {
                continue;
            }

            $rows[] = [
                'ID' => $rental->id,
                'Invoice' => $rental->invoice_number,
                'Paid Amount' => $rental->paid_amount . ' → ' . $totalPaidForRental,
                'Fine Paid' => $rental->fine_paid_amount . ' → ' . $totalPaidForFine,
                'Payment Status' => $rental->payment_status . ' → ' . $paymentStatus,
                'Fine Status' => $rental->fine_status . ' → ' . $fineStatus,
            ];

            if ($isForce) {
                $rental->update([
                    'paid_amount' => $totalPaidForRental,
                    'fine_paid_amount' => $totalPaidForFine,
                    'payment_status' => $paymentStatus,
                    'fine_status' => $fineStatus,
                    'change_amount' => $changeAmount,
                ]);
                $updateCount++;
            }
        }

        if ($rows === []) {
            $this->info('No rentals need updating. All payment statuses are already correct.');
            return 0;
        }

        $this->table(
            ['ID', 'Invoice', 'Paid Amount', 'Fine Paid', 'Payment Status', 'Fine Status'],
            $rows
        );

        if ($isForce) {
            $this->info("Backfill completed. {$updateCount} rental(s) updated.");
        } else {
            $this->info('This was a dry run. No changes were made to the database.');
            $this->info('Run with --force to apply changes.');
        }

        return 0;
    }
}
