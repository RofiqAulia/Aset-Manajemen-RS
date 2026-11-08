<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Determine whether the user can view any assets.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isAssetStaff()
            || $user->isUnitHead()
            || $user->isManagement();
    }

    /**
     * Determine whether the user can view the asset.
     */
    public function view(User $user, Asset $asset): bool
    {
        if ($user->isAdmin() || $user->isAssetStaff() || $user->isManagement()) {
            return true;
        }

        if ($user->isUnitHead()) {
            return $user->department_id === null || $asset->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create assets.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('assets.create');
    }

    /**
     * Determine whether the user can update the asset.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->isAdmin() || $user->hasPermission('assets.update');
    }

    /**
     * Determine whether the user can delete the asset.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete assets.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
