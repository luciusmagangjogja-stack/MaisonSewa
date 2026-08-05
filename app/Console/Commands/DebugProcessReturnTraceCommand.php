<?php

namespace App\Console\Commands;

use App\Models\Rental;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\RentalService;
use Throwable;

class DebugProcessReturnTraceCommand extends Command
{
    protected $signature = 'debug:process-return-trace {rentalId? : rental id} {--dry-run : do not call real return, only trace current state}';
    protected $description = 'Trace processReturn() flow with step-by-step evidence (read-only by default with --dry-run). Writes only if --dry-run is not used.';

    public function handle(): int
    {
        $rentalId = (int) $this->argument('rentalId');
        $dryRun = (bool) $this->option('dry-run');

        $user = Auth::user();
        $this->line('=== PROCESS RETURN TRACE ===');
        $this->line('Authenticated User');
        $this->line('- id: ' . ($user?->id ?? 'null'));
        $this->line('- role: ' . ($user?->role ?? 'null'));
        $this->line('- branch_id: ' . ($user?->branch_id ?? 'null'));
        $this->line('');

        $rental = Rental::query()->with(['items.product'])->find($rentalId);
        $this->line('1) Rental found?');
        if (!$rental) {
            $this->error('Rental not found for id=' . $rentalId);
            return 1;
        }
        $this->info('YES');
        $this->line('rental_id=' . $rental->id . ' invoice=' . $rental->invoice_number);
        $this->line('');

        $items = $rental->items ?? collect();
        $this->line('2) Loop rental_items');
        $this->line('items_count=' . $items->count());
        $this->line('');

        $this->line('3) Per item - BEFORE');
        foreach ($items as $item) {
            $product = $item->product;
            $this->line('---');
            $this->line('product_id=' . ($product?->id ?? 'null'));
            $this->line('quantity=' . ($item->quantity ?? 'null'));
            $this->line('product.stock_available(before)=' . ($product?->stock_available ?? 'null'));
            $this->line('product.status(before)=' . ($product?->status ?? 'null'));
        }
        $this->line('');

        if ($dryRun) {
            $this->line('DRY RUN mode: NOT calling RentalService::processReturn()');
            $this->line('');

            $this->line('5) Condition checks (as coded)');
            foreach ($items as $item) {
                $product = $item->product;
                $beforeStock = (int) ($product?->stock_available ?? 0);
                $beforeStatus = (string) ($product?->status ?? '');
                $afterStock = $beforeStock + (int) ($item->quantity ?? 0);

                $cond1 = $beforeStatus === 'rented';
                $cond2 = $afterStock > 0;

                $this->line('---');
                $this->line('product_id=' . ($product?->id ?? 'null'));
                $this->line('status===\'rented\' ? ' . ($cond1 ? 'TRUE' : 'FALSE'));
                $this->line('afterStock(=before+qty) > 0 ? ' . ($cond2 ? 'TRUE' : 'FALSE'));
                $this->line('update_status_will_happen? ' . (($cond1 && $cond2) ? 'TRUE' : 'FALSE'));
            }

            $this->line('========================');
            return 0;
        }

        $this->line('4) Calling RentalService::processReturn() (NOT dry-run)');

        $service = app(RentalService::class);
        $data = ['items' => []];

        try {
            DB::beginTransaction();
            $service->processReturn($rental, $data);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            $this->error('EXCEPTION: ' . get_class($e) . ' - ' . $e->getMessage());
            $this->line('');
            $this->line($e->getTraceAsString());
            return 1;
        }

        $this->line('6) update() called?');
        $this->line('Cannot confirm directly without instrumenting, but will verify via fresh() below.');
        $this->line('');

        $this->line('7) Per item - AFTER (fresh from DB)');
        foreach ($items as $item) {
            $product = Product::query()->find($item->product_id);
            $this->line('---');
            $this->line('id=' . ($product?->id ?? 'null'));
            $this->line('status(after)=' . ($product?->status ?? 'null'));
            $this->line('stock_available(after)=' . ($product?->stock_available ?? 'null'));
        }

        $this->line('');
        $this->line('TRANSACTION CHECK');
        $this->line('processReturn() uses DB::transaction internally, so nested transaction/rollback behavior may vary. Any exception would be reported above.');

        $this->line('========================');
        return 0;
    }
}

