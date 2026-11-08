<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AssetMovement;
use App\Models\User;

class AssetMovementPolicy
{
    /**
     * Determine whether the user can view any asset movements.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('assets.mutate')
            || $user->hasPermission('movements.approve')
            || $user->isManagement();
    }

    /**
     * Determine whether the user can view the asset movement.
     */
    public function view(User $user, AssetMovement $movement): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create asset movements.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('assets.mutate')
            || $user->hasPermission('movements.approve');
    }

    /**
     * Determine whether the user can update the asset movement.
     */
    public function update(User $user, AssetMovement $movement): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('assets.mutate')
            || $user->hasPermission('movements.approve');
    }

    /**
     * Determine whether the user can delete the asset movement.
     */
    public function delete(User $user, AssetMovement $movement): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete asset movements.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
