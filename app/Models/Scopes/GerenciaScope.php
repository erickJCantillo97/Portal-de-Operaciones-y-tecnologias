<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Spatie\Permission\Models\Role;

class GerenciaScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $role = Role::first()->name;
        if (auth()->user() && !auth()->user()->hasRole($role))
            $builder->where('gerencia', auth()->user()->gerencia);
    }
}
