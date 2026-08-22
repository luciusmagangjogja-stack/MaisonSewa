<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function data(Request $request)
    {
        $user = $request->user();

        $filters = [
            'scope_type' => $user->role,
            'branch_id' => $request->input('branch_id'),
            'sales_user_id' => $request->input('sales_user_id'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        // For sales, force to their own id
        if ($user->role === 'sales') {
            $filters['sales_user_id'] = $user->id;
            $filters['branch_id'] = $user->branch_id;
        }

        // For admin_toko, force to their own branch
        if ($user->role === 'admin_toko') {
            $filters['branch_id'] = $user->branch_id;
        }

        $payload = $this->dashboardService->buildPayload($filters);
        return response()->json($payload, 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'super_admin' => $this->superAdminDashboard($user),
            'admin_toko'  => $this->adminDashboard($user),
            'sales'       => $this->salesDashboard($user),
            default       => abort(403),
        };
    }


    private function superAdminDashboard($user)
    {
        $branches = \App\Models\Branch::all();
        $sales = \App\Models\User::where('role', 'sales')->get();
        $payload = $this->dashboardService->getDashboardDataForUser($user);
        $stats = $payload['stats'] ?? [];
        return view('dashboard.super-admin', compact('stats', 'branches', 'sales'));
    }

    private function adminDashboard($user)
    {
        $branches = \App\Models\Branch::where('id', $user->branch_id)->get();
        $sales = \App\Models\User::where('role', 'sales')->where('branch_id', $user->branch_id)->get();
        $payload = $this->dashboardService->getDashboardDataForUser($user);
        $stats = $payload['stats'] ?? [];
        return view('dashboard.admin', compact('stats', 'branches', 'sales'));
    }

    private function salesDashboard($user)
    {
        $branches = \App\Models\Branch::where('id', $user->branch_id)->get();
        $sales = \App\Models\User::where('id', $user->id)->get();
        $payload = $this->dashboardService->getDashboardDataForUser($user);
        $stats = $payload['stats'] ?? [];
        return view('dashboard.sales', compact('stats', 'branches', 'sales'));
    }

}

