<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\MaterialRequirementController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\WareHouse\AssignmentToolController;
use App\Http\Controllers\WareHouse\CategoryController;
use App\Http\Controllers\WareHouse\ConsumableController;
use App\Http\Controllers\WareHouse\MaterialController;
use App\Http\Controllers\WareHouse\ToolController;
use App\Http\Controllers\WarehouseController;
use App\Models\WareHouse\AssignmentTool;
use App\Models\WareHouse\Requirement;
use App\Models\WareHouse\Tool;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::get('tools/report', [ToolController::class, 'reports'])->name('tools.reports');
    Route::resource('tools', ToolController::class);
    Route::resource('assignmentTool', AssignmentToolController::class);
    Route::resource('warehouse', WarehouseController::class);


    Route::get('requirements/getRequirementByRole', [RequirementController::class, 'getRequirementByRole'])->name('get.requirements.role');
    Route::resource('requirements', RequirementController::class);
    Route::resource('consumable', ConsumableController::class);
    Route::get('materials/{requirement}', [MaterialRequirementController::class, 'index'])->name('materials.index');
    Route::get('manageRequeriments', [RequirementController::class, 'manageRequirements'])->name('manage.requirements');
    Route::post('storeRequirementOficials', [RequirementController::class, 'storeRequirementOficials'])->name('store.requirement.oficial');
    Route::post('aproveRequirement/{requirement}', [RequirementController::class, 'aproveRequirement'])->name('aprove.requirement');

    // Route::get('categorias_anteriores', [CategoryController::class, 'getDataAnterior']);
    // Route::get('equipos_anteriores', [ToolController::class, 'getDataAnterior']);
    // Route::get('prestamos_anteriores', [AssignmentToolController::class, 'getDataAnterior']);
});
