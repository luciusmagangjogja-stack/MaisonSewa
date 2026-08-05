<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AuditPaymentsCommand extends Command
{
    protected $signature = 'audit:payments {--limit=200} {--fix=false : not used yet (placeholder)}';
    protected $description = 'Audit payments consistency: rentals with paid_amount/payment_status but without payments rows.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        if ($limit <= 0) {
            $limit = 200;
        }

        $this->line('=== Audit Payments (SewaJas) ===');
        $this->line('Limit rows output: ' . $limit);

        $criteria1 = $this->baseRentalsWithoutPaymentsQuery();
        $criteria1->where(function (Builder $q) {
            $q->where('paid_amount', '>', 0)
                ->orWhereIn('payment_status', ['paid', 'partial']);
        });

        $criteria2 = $this->baseRentalsWithoutPaymentsQuery();
        $criteria2->whereIn('rental_status', ['active', 'returned', 'overdue']);

        $count1 = (clone $criteria1)->count();
        $count2 = (clone $criteria2)->count();

        $this->newLine();
        $this->info('Kriteria 1 (paid_amount > 0 atau payment_status paid/partial) DAN payments count = 0');
        $this->line('Jumlah rental match: ' . $count1);
        $this->renderSample($criteria1, $limit);

        $this->newLine();
        $this->info('Kriteria 2 (rental_status active/returned/overdue) DAN payments count = 0');
        $this->line('Jumlah rental match: ' . $count2);
        $this->renderSample($criteria2, $limit);

        $this->newLine();
        $this->warn('Catatan: identifikasi demo vs non-demo saat ini belum bisa dipastikan karena tidak ada kolom penanda demo baku yang ditemukan di skema rentals. Laporan akan berbasis created_by/branch untuk inferensi bila diperlukan setelah audit pertama dipahami.');

        return 0;
    }

    private function baseRentalsWithoutPaymentsQuery(): Builder
    {
        return Rental::query()
            ->withCount('payments')
            ->where('branch_id', '>', 0)
            ->whereHas('customer')
            ->where('deleted_at', null) // just in case; SoftDeletes
            ->where('paid_amount', '>=', 0)
            ->where(function (Builder $q) {
                // payments_count = 0
                // withCount alias is payments_count
            })
            ->having('payments_count', '=', 0);
    }

    private function renderSample(Builder $query, int $limit): void
    {
        $rows = (clone $query)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get([
                'invoice_number',
                'rental_status',
                'payment_status',
                'total_amount',
                'paid_amount',
                'branch_id',
                'created_by',
                'created_at',
            ]);

        if ($rows->isEmpty()) {
            $this->line('Tidak ada data untuk ditampilkan (sample kosong).');
            return;
        }

        $this->line('--- Sample results ---');

        $this->table([
            'invoice_number',
            'rental_status',
            'payment_status',
            'total_amount',
            'paid_amount',
            'branch_id',
            'created_by',
            'created_at',
        ], $rows->map(fn ($r) => [
            $r->invoice_number,
            $r->rental_status,
            $r->payment_status,
            $r->total_amount,
            $r->paid_amount,
            $r->branch_id,
            $r->created_by,
            $r->created_at,
        ])->toArray());
    }
}

