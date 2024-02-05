<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\WareHouse\AssignmentToolController;
use App\Http\Controllers\WareHouse\CategoryController;
use App\Http\Controllers\WareHouse\ToolController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('tools', ToolController::class);
    Route::resource('assignmentTool', AssignmentToolController::class);

    Route::get('categorias_anteriores', [CategoryController::class, 'getDataAnterior']);
    Route::get('equipos_anteriores', [ToolController::class, 'getDataAnterior']);
});
