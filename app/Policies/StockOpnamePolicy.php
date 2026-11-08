<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockOpname;
use App\Models\User;

class StockOpnamePolicy
{
    /**
     * Determine whether the user can view any stock opnames.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('stock_opname.manage')
            || $user->isManagement();
    }

    /**
     * Determine whether the user can view the stock opname.
     */
    public function view(User $user, StockOpname $stockOpname): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('stock_opname.manage')
            || $user->isManagement();
    }

    /**
     * Determine whether the user can create stock opnames.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('stock_opname.manage');
    }

    /**
     * Determine whether the user can update the stock opname.
     */
    public function update(User $user, StockOpname $stockOpname): bool
    {
        return $user->isAdmin() || $user->hasPermission('stock_opname.manage');
    }

    /**
     * Determine whether the user can delete the stock opname.
     */
    public function delete(User $user, StockOpname $stockOpname): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete stock opnames.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
