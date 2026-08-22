<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $salesQuery = User::where('role', 'sales')
            ->with('branch');

        if ($user->isSales()) {
            $salesQuery->where('id', $user->id);
        }

        if (!$user->isSales() && $request->filled('branch_id')) {
            $salesQuery->where('branch_id', $request->input('branch_id'));
        }

        $salesList = $salesQuery->orderByDesc('total_points')->paginate(20);

        $totalPoints = (clone $salesQuery)->sum('total_points');

        $branches = Branch::when($user->isAdminToko(), fn($q) => $q->where('id', $user->branch_id))->get();

        return view('points.index', compact('salesList', 'totalPoints', 'branches'));
    }
}
