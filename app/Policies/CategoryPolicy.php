<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin_toko', 'sales']);
    }

    public function view(User $user, Category $category): bool
    {
        return $user->isSuperAdmin() || $user->isAdminToko() || $user->isSales();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->isSuperAdmin() || $user->isAdminToko();
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isSuperAdmin();
    }
}
