<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Models\Payment;
use App\Models\RentalItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditRentalInvoiceOriginCommand extends Command
{
    protected $signature = 'audit:rental-origin {invoice : Rental invoice_number}';
    protected $description = 'Investigate whether a rental likely comes from DemoDataSeeder vs manual/origin (best-effort heuristics). Does not modify data.';

    public function handle(): int
    {
        $invoice = (string) $this->argument('invoice');

        $rental = Rental::query()->with(['createdBy', 'branch', 'items', 'payments', 'customer'])->where('invoice_number', $invoice)->first();

        if (!$rental) {
            $this->error("Rental not found: {$invoice}");
            return 1;
        }

        $this->line('=== Audit Rental Origin (heuristics) ===');
        $this->info("Invoice: {$rental->invoice_number}");

        // 1) Invoice format check
        $this->line('--- Invoice format check ---');
        $this->table(
            ['field', 'value'],
            [
                ['rental_date', (string) $rental->rental_date],
                ['created_at', (string) $rental->created_at],
                ['created_by_id', (string) ($rental->created_by ?? ($rental->createdBy?->id ?? 'null'))],
                ['created_by_name', (string) ($rental->createdBy?->name ?? '')],
                ['branch_id', (string) $rental->branch_id],
                ['branch_code', (string) $rental->branch?->code],
            ]
        );

        $this->line('--- Payments consistency snapshot ---');
        $paymentsCount = $rental->payments->count();
        $paymentsSum = (float) ($rental->payments->sum('amount') ?? 0);
        $this->table(
            ['field', 'value'],
            [
                ['payment_status', (string) $rental->payment_status],
                ['paid_amount', (string) $rental->paid_amount],
                ['payments_count', (string) $paymentsCount],
                ['payments_sum_amount', (string) $paymentsSum],
            ]
        );

        $this->line('--- Items snapshot ---');
        $this->table(
            ['product_id', 'product_name', 'qty', 'duration_days', 'item_subtotal'],
            $rental->items->map(fn ($it) => [
                (string) $it->product_id,
                (string) $it->product_name,
                (string) $it->quantity,
                (string) $it->duration_days,
                (string) $it->subtotal,
            ])->toArray()
        );

        // 2) Heuristic: DemoDataSeeder uses INV{Ymd}{branchId}{4-digit seq}
        $this->line('--- Heuristics ---');
        $isDemoInvoicePattern = preg_match('/^INV\d{8}[12]\d{4}$/', (string) $rental->invoice_number) === 1;

        // RentalDemoSeeder uses RENT/<branch_code>/<year>/<5-digit>
        $isRentalDemoInvoicePattern = preg_match('#^RENT/.+/.+/.+$#', (string) $rental->invoice_number) === 1;

        $likelyDemo = false;
        $reasons = [];

        if ($isDemoInvoicePattern) {
            $likelyDemo = true;
            $reasons[] = 'invoice_number cocok pola DemoDataSeeder: INV{Ymd}{branchId}{0001-9999}';
        }
        if ($isRentalDemoInvoicePattern) {
            $likelyDemo = true;
            $reasons[] = 'invoice_number cocok pola RentalDemoSeeder: RENT/<branch_code>/<year>/<seq> (rentals demo)';
        }

        // Additional heuristics: demo rentals tend to have created_by sales user IDs 4 (branch 1) and 5 (branch 2) per DemoDataSeeder
        if ($rental->created_by && in_array((int) $rental->created_by, [4, 5], true)) {
            $likelyDemo = $likelyDemo || true;
            $reasons[] = 'created_by berada di range sales demo user yang sering dipakai DemoDataSeeder (4/5)';
        }

        // 3) Report best-effort
        $this->line('--- Result ---');
        $this->info('likely_demo (heuristic): ' . ($likelyDemo ? 'YES' : 'NO/UNCLEAR'));
        if (!empty($reasons)) {
            foreach ($reasons as $r) {
                $this->line(' - ' . $r);
            }
        } else {
            $this->line(' - Tidak ada indikator pola demo yang kuat.' );
        }

        $this->line('Catatan: ini heuristics berbasis pola invoice_number & user_id; belum “hard proof”. Untuk keputusan delete/insert wajib gunakan hasil investigasi yang kamu minta (sebelum eksekusi).');

        return 0;
    }
}

