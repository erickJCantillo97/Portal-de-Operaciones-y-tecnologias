<?php

namespace App\Policies;

use App\Models\Projects\Project;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {

        return is_numeric(array_search('projects read', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        dd(is_numeric(array_search('projects edit', $user->getAllPermissions()->pluck('name')->toArray())));
        return is_numeric(array_search('projects read', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return is_numeric(array_search('projects create', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        return is_numeric(array_search('projects edit', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {

        return is_numeric(array_search('projects delete', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        //
    }
}
