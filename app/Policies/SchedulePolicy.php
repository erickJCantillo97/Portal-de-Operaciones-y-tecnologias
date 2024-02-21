<?php

namespace App\Policies;

use App\Models\User;

class SchedulePolicy
{
    /**
     * Create a new policy instance.
     */
    public function create(User $user): bool
    {
        return is_numeric(array_search('schedule create', $user->getAllPermissions()->pluck('name')->toArray()));
    }
}
