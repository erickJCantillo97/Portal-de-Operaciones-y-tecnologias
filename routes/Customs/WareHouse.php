<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\WareHouse\AssignmentToolController;
use App\Http\Controllers\WareHouse\CategoryController;
use App\Http\Controllers\WareHouse\ToolController;
use App\Http\Controllers\WarehouseController;
use App\Models\WareHouse\AssignmentTool;
use App\Models\WareHouse\Requirement;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('tools', ToolController::class);
    Route::resource('assignmentTool', AssignmentToolController::class);
    Route::resource('warehouse', WarehouseController::class);


    Route::resource('requirements', RequirementController::class);
    // Route::get('categorias_anteriores', [CategoryController::class, 'getDataAnterior']);
    // Route::get('equipos_anteriores', [ToolController::class, 'getDataAnterior']);
    // Route::get('prestamos_anteriores', [AssignmentToolController::class, 'getDataAnterior']);
});
