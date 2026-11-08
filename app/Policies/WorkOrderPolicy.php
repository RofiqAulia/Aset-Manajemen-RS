<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    /**
     * Determine whether the user can view any work orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('work_orders.handle')
            || $user->hasPermission('tickets.report')
            || $user->isManagement();
    }

    /**
     * Determine whether the user can view the work order.
     */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create work orders.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('tickets.report');
    }

    /**
     * Determine whether the user can update the work order.
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $user->isAdmin()
            || $user->hasPermission('work_orders.handle')
            || $user->hasPermission('tickets.confirm');
    }

    /**
     * Determine whether the user can delete the work order.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can bulk delete work orders.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
