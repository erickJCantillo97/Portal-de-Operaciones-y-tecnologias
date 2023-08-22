<?php

use App\Http\Controllers\SWBS\BaseActivityController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\Security\RoleController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


Route::middleware(['auth:sanctum', config('jetstream.auth_session'),'verified'])->group(function () {

    Route::get('', function (){
        return Inertia::render('Settings/Basic/Index');
    })->name('settings.basic');

    Route::resource('baseActivities', BaseActivityController::class);

    Route::get('gerencias/get', function (){
        return response()->json([gerencias()], 200);
    })->name('gerencias.index');

    Route::get('gruposConstructivos/get', function (){
        return response()->json([gruposConstructivos()], 200);
    })->name('gruposConstructivos.index');

});
