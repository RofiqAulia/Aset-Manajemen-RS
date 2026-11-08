<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MasterDataPolicy
{
    /**
     * Determine whether the user can view any master data records.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->isAssetStaff();
    }

    /**
     * Determine whether the user can view the master data record.
     */
    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create master data records.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the master data record.
     */
    public function update(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the master data record.
     */
    public function delete(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete master data records.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
