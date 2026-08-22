<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Rental;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    // Daftar role tersedia (tanpa Spatie)
    private array $roles = ['super_admin', 'admin_toko', 'sales'];

    public function index(Request $request)
    {
        $users = User::with('branch')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role,   fn($q) => $q->where('role', $request->role))
            ->when($request->branch, fn($q) => $q->where('branch_id', $request->branch))
            ->latest()
            ->paginate(15)->withQueryString();

        $branches = Branch::where('is_active', true)->get();
        $roles    = $this->roles;
        $roleCounts = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        return view('users.index', compact('users', 'branches', 'roles', 'roleCounts'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $roles    = $this->roles;
        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'phone'     => 'nullable|regex:/^[0-9]+$/|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'role'      => 'required|in:super_admin,admin_toko,sales',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'phone'     => $data['phone'] ?? null,
            'branch_id' => $data['role'] === 'super_admin' ? null : $data['branch_id'],
            'role'      => $data['role'],
            'avatar'    => $avatarPath,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} berhasil ditambahkan!");
    }

    public function edit(User $user)
    {
        $branches = Branch::where('is_active', true)->get();
        $roles    = $this->roles;
        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'email'     => "required|email|unique:users,email,{$user->id}",
            'password'  => 'nullable|string|min:8|confirmed',
            'phone'     => 'nullable|regex:/^[0-9]+$/|max:20',
            'branch_id' => 'nullable|exists:branches,id',
            'role'      => 'required|in:super_admin,admin_toko,sales',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $updateData = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'branch_id' => $data['role'] === 'super_admin' ? null : $data['branch_id'],
            'role'      => $data['role'],
            'avatar'    => $avatarPath,
            'password'  => !empty($data['password']) ? Hash::make($data['password']) : $user->password,
        ];

        $user->update($updateData);

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} berhasil diperbarui!");
    }

    public function show(User $user)
    {
        $user->load(['branch', 'rentals' => fn($q) => $q->latest()->take(10)]);
        $rentalCount = Rental::where('created_by', $user->id)->count();
        $activityLogs = ActivityLog::where('user_id', $user->id)->latest()->take(10)->get();
        return view('users.show', compact('user', 'rentalCount', 'activityLogs'));
    }

    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $oldActive = $user->is_active;
        $user->update(['is_active' => !$oldActive]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        // Audit log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch_id,
            'action' => $user->is_active ? 'activate_user' : 'deactivate_user',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => auth()->user()->name . ' ' . $status . ' user ' . $user->name,
            'old_values' => ['is_active' => $oldActive],
            'new_values' => ['is_active' => $user->is_active],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    public function destroy(User $user)
    {
        // 1. Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // 2. Prevent deleting last Super Admin
        if ($user->isSuperAdmin()) {
            $superAdminCount = User::where('role', 'super_admin')->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Tidak bisa menghapus Super Admin terakhir.');
            }
        }

        // 3. Check for related records → deactivate instead
        $hasRentalsCreated  = Rental::where('created_by', $user->id)->withTrashed()->exists();
        $hasRentalsReturned = Rental::where('returned_by', $user->id)->withTrashed()->exists();
        $hasPayments        = Payment::where('received_by', $user->id)->exists();
        $hasLogs            = ActivityLog::where('user_id', $user->id)->exists();
        $hasRentals         = $hasRentalsCreated || $hasRentalsReturned;

        if ($hasRentals || $hasPayments || $hasLogs) {
            $user->update(['is_active' => false]);
            try {
                $user->delete(); // soft delete
            } catch (QueryException $e) {
                if ($e->getCode() === '23000') {
                    return back()->with('error', 'User tidak dapat dihapus karena masih terhubung dengan data transaksi lain di database.');
                }
                throw $e;
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'branch_id' => auth()->user()->branch_id,
                'action' => 'deactivate_user',
                'model_type' => User::class,
                'model_id' => $user->id,
                'description' => auth()->user()->name . ' menonaktifkan user ' . $user->name . ' (memiliki riwayat transaksi)',
                'old_values' => ['is_active' => true],
                'new_values' => ['is_active' => false],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return back()->with('success', "User {$user->name} telah dinonaktifkan karena memiliki riwayat transaksi.");
        }

        // 4. No related records → safe to soft delete
        try {
            $user->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->with('error', 'User tidak dapat dihapus karena masih terhubung dengan data transaksi lain di database.');
            }
            throw $e;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch_id,
            'action' => 'delete_user',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => auth()->user()->name . ' menghapus user ' . $user->name,
            'old_values' => $user->getAttributes(),
            'new_values' => [],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} berhasil dihapus.");
    }
}
