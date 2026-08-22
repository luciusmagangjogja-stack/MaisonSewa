<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Rental;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = auth()->user();
            $branchId = $user?->branch_id;

            if (!$user) {
                $view->with('overdueCount', 0);
                $view->with('unreadNotif', 0);
                return;
            }

            $overdueQuery = Rental::where('rental_status', 'overdue');

            if ($user->role === 'sales') {
                $overdueQuery->where('created_by', $user->id);
            } elseif (!$user->isSuperAdmin()) {
                $overdueQuery->where('branch_id', $branchId);
            }

            $overdueCount = $overdueQuery->count();

            $unreadNotif = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();

            $view->with('overdueCount', $overdueCount);
            $view->with('unreadNotif', $unreadNotif);
        });
    }
}
