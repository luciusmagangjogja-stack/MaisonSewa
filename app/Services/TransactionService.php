<?php

namespace App\Services;

use App\Models\Rental;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function list(Request $request): array
    {
        $user = Auth::user();

        $query = Rental::with(['customer', 'branch', 'items', 'payments']);

        if ($user->role === 'sales') {
            $query->where('created_by', $user->id);
        } elseif (!$user->isSuperAdmin()) {
            $query->where('branch_id', $user->branch_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c
                      ->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        $transactions = $query->latest('rental_date')->paginate(15)->withQueryString();

        return ['transactions' => $transactions];
    }

    public function detail(Rental $transaction): array
    {
        $transaction->load(['customer', 'branch', 'items.product.category', 'payments.receivedBy', 'activityLogs.user']);

        return [
            'payments' => $transaction->payments,
            'activity_logs' => $transaction->activityLogs,
        ];
    }

    public function update(Rental $transaction, array $data): void
    {
        DB::transaction(function () use ($transaction, $data) {
            $old = $transaction->only(['notes', 'rental_status']);

            $transaction->update([
                'notes' => $data['notes'] ?? $transaction->notes,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'branch_id' => Auth::user()?->branch_id,
                'action' => 'update_transaction',
                'model_type' => Rental::class,
                'model_id' => $transaction->id,
                'description' => Auth::user()->name . ' mengubah transaksi',
                'old_values' => $old,
                'new_values' => ['notes' => $transaction->notes],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }

    public function delete(Rental $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $old = $transaction->getAttributes();
            $transaction->delete();

            ActivityLog::create([
                'user_id' => Auth::id(),
                'branch_id' => Auth::user()?->branch_id,
                'action' => 'delete_transaction',
                'model_type' => Rental::class,
                'model_id' => $transaction->id,
                'description' => Auth::user()->name . ' menghapus transaksi',
                'old_values' => $old,
                'new_values' => [],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}

