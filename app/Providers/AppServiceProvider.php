<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
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

            $overdueCount = Cache::remember("overdue_count_{$branchId}_{$user?->id}", 60, function () use ($user, $branchId) {
                if (!$user) {
                    return 0;
                }
                return Rental::where('rental_status', 'overdue')
                    ->when(!$user->isSuperAdmin(), fn($q) => $q->where('branch_id', $branchId))
                    ->count();
            });

            $unreadNotif = Cache::remember("unread_notif_{$user?->id}", 60, function () use ($user) {
                if (!$user) {
                    return 0;
                }
                return Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count();
            });

            $view->with('overdueCount', $overdueCount);
            $view->with('unreadNotif', $unreadNotif);
        });
    }
}
