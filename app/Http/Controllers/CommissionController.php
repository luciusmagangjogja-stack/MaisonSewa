<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Rental::with(['customer', 'createdBy', 'returnedBy', 'items'])
            ->where(function ($q) {
                $q->where('commission_status_serah', 'earned')
                  ->orWhere('commission_status_kembali', 'earned');
            })
            ->where(function ($q) {
                $q->where('commission_amount_serah', '>', 0)
                  ->orWhere('commission_amount_kembali', '>', 0);
            });

        if ($user->isSales()) {
            $query->where('created_by', $user->id);
        }

        if (!$user->isSales() && $request->filled('sales_id')) {
            $query->where('created_by', $request->input('sales_id'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('rental_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('rental_date', '<=', $request->input('end_date'));
        }

        if ($user->isSales()) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        $rentals = $query->orderByDesc('rental_date')->paginate(20);

        $totalCommission = (clone $query)->sum(DB::raw('COALESCE(commission_amount_serah,0) + COALESCE(commission_amount_kembali,0)'));
        $totalTransactions = (clone $query)->count();

        $salesList = User::where('role', 'sales')
            ->when($user->isAdminToko(), fn($q) => $q->where('branch_id', $user->branch_id))
            ->get(['id', 'name', 'branch_id', 'commission_rate_serah', 'commission_rate_kembali']);

        $branchId = $user->isSales() ? $user->branch_id : ($request->input('branch_id') ?? null);
        $branches = Branch::when($user->isAdminToko(), fn($q) => $q->where('id', $user->branch_id))->get();

        return view('commissions.index', compact('rentals', 'totalCommission', 'totalTransactions', 'salesList', 'branches', 'branchId'));
    }
}
