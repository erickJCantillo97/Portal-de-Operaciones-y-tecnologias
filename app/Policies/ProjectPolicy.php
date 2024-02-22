<?php

namespace App\Policies;

use App\Models\Projects\Project;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class ProjectPolicy
{

    public function viewAny(User $user): bool
    {
        return is_numeric(array_search('projects read', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    public function view(User $user, Project $project): bool
    {
        return is_numeric(array_search('projects read', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    public function create(User $user): bool
    {
        return is_numeric(array_search('projects create', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    public function update(User $user, Project $project): bool
    {
        return is_numeric(array_search('projects edit', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    public function delete(User $user, Project $project): bool
    {
        return is_numeric(array_search('projects delete', $user->getAllPermissions()->pluck('name')->toArray()));
    }

    public function restore(User $user, Project $project): bool
    {
        //
    }

    public function forceDelete(User $user, Project $project): bool
    {
        //
    }
}
