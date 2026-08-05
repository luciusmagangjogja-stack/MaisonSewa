<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class DebugProductsCommand extends Command
{
    protected $signature = 'debug:products';
    protected $description = 'Read-only diagnosis for product dropdown filters on rentals.create';

    public function handle(): int
    {
        $user = Auth::user();

        $this->line('===== PRODUCT DEBUG =====');

        $authCheck = Auth::check();
        $this->line('Authenticated User');
        $this->line('- id: ' . ($user?->id ?? 'null'));
        $this->line('- role: ' . ($user?->role ?? 'null'));
        $this->line('- branch_id: ' . ($user?->branch_id ?? 'null'));
        $this->line('');

        // Use authenticated branch if available, otherwise default to 1 for diagnosis consistency
        $branchId = $user?->branch_id ?? 1;

        $baseQuery = Product::query();
        $totalProducts = (clone $baseQuery)->count();

        $this->line('Total Products');
        $this->line($totalProducts);
        $this->line('');

        $afterBranchQuery = $baseQuery;
        $branchApplied = false;
        if ($authCheck && $user && !$user->isSuperAdmin()) {
            $branchApplied = true;
            $afterBranchQuery->where('branch_id', $branchId);
        }

        $afterBranchCount = (clone $afterBranchQuery)->count();

        $this->line('After Branch Filter');
        $this->line($afterBranchCount);
        $this->line('');

        $afterStatusQuery = (clone $afterBranchQuery)->where('status', 'available');
        $afterStatusCount = (clone $afterStatusQuery)->count();

        $this->line("After Status Filter (status='available')");
        $this->line($afterStatusCount);
        $this->line('');

        $afterStockQuery = (clone $afterStatusQuery)->where('stock_available', '>', 0);
        $afterStockCount = (clone $afterStockQuery)->count();

        $this->line('After Stock Filter (stock_available > 0)');
        $this->line($afterStockCount);
        $this->line('');

        $finalCount = (clone $afterStockQuery)->count();

        $this->line('Final Products Count');
        $this->line($finalCount);
        $this->line('');

        // REQUIRED: print all rows for branch_id=1 (read-only inspection)
        $this->line('Branch Products (branch_id=1)');
        $rows = Product::where('branch_id', 1)
            ->select(['id', 'name', 'branch_id', 'status', 'stock_available'])
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            $this->line('(no rows)');
        } else {
            foreach ($rows as $p) {
                $this->line('- ' . $p->id . ' | ' . $p->name . ' | ' . $p->branch_id . ' | ' . $p->status . ' | ' . $p->stock_available);
            }
        }

        $this->line('');

        // Also keep a sample of final query for quick view
        $sample = (clone $afterStockQuery)->limit(10)
            ->get(['id', 'name', 'branch_id', 'status', 'stock_total', 'stock_available']);

        $this->line('Sample Products (max 10)');
        if ($sample->isEmpty()) {
            $this->line('(no rows)');
        } else {
            foreach ($sample as $p) {
                $this->line('- ' . $p->id . ' | ' . $p->name . ' | ' . $p->branch_id . ' | ' . $p->status . ' | ' . $p->stock_total . ' | ' . $p->stock_available);
            }
        }

        $this->line('=========================');

        return self::SUCCESS;
    }
}


