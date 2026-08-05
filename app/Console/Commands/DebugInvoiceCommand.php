<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;

class DebugInvoiceCommand extends Command
{
    protected $signature = 'rental:debug-invoice {invoice : invoice_number (e.g. INV2026060710010)}';
    protected $description = 'Debug an invoice by printing rental(s) (including soft-deleted) and counts of rental_items/payments.';

    public function handle(): int
    {
        $invoice = (string) $this->argument('invoice');

        $rentals = Rental::query()
            ->withTrashed()
            ->with(['items', 'payments'])
            ->where('invoice_number', $invoice)
            ->get();

        if ($rentals->isEmpty()) {
            $this->warn("No rental found for invoice: {$invoice}");
            return 0;
        }

        $this->info("Found rentals: " . $rentals->count());
        foreach ($rentals as $rental) {
            $itemsCount = $rental->items->count();
            $paymentsCount = $rental->payments->count();

            $this->newLine();
            $this->info('---');
            $this->info('Rental id: ' . $rental->id);

            $this->table(
                ['field', 'value'],
                [
                    ['invoice_number', $rental->invoice_number],
                    ['deleted?(trashed)', $rental->trashed() ? 'yes' : 'no'],
                    ['rental_status', $rental->rental_status],
                    ['payment_status', $rental->payment_status],
                    ['total_amount', (string) $rental->total_amount],
                    ['paid_amount', (string) $rental->paid_amount],
                    ['rental_items_count', (string) $itemsCount],
                    ['payments_count', (string) $paymentsCount],
                    ['branch_id', (string) $rental->branch_id],
                    ['created_by', (string) $rental->created_by],
                    ['created_at', (string) $rental->created_at],
                ]
            );
        }

        return 0;
    }
}

