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
    Route::resource('permissions', PermissionController::class)->except('index');
    Route::post('assignmentRoleToUser/{user}',  [RoleController::class, 'assignmentRoleToUser'])->name('assignment.role.user');
    Route::post('removeRoleToUser/{user}',  [RoleController::class, 'removeRoleToUser'])->name('remove.role.user');
    Route::get('',  [PermissionController::class, 'index'])->name('security');
});
