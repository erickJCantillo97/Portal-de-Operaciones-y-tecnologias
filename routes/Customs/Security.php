<?php

use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('',  function () {
        $users = User::with('roles')->orderBy('gerencia')->get()->map(function ($user) {
            $roles = collect($user['roles'])->pluck('name')->toArray();
            return [
                'name' => $user['name'],
                'photo' => $user['photo'],
                'cargo' => $user['cargo'],
                'gerencia' => $user['gerencia'],
                'roles' => count($roles) == 0 ? ["Sin rol"] : $roles,
            ];
        });
        $roles = Role::with('permissions')->get()->map(function ($r) {
            return [
                'id' => $r['id'],
                'name' => $r['name'],
                'users' => User::role($r['name'])->get(),
                'permissions' => $r['permissions']
            ];
        });
        $permisos = Permission::orderBy('name')->get();

        return Inertia::render('Security/Index', [
            'users' => $users, 'roles' => $roles, 'permisos' => $permisos
        ]);
    })->name('security');
});
