<?php

use App\Http\Controllers\Projects\AuthorizationController;
use App\Http\Controllers\Projects\ContractController;
use App\Http\Controllers\Projects\CustomerController;
use App\Http\Controllers\Projects\GanttController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\QuoteController;
use App\Http\Controllers\Projects\ShipController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/cronograma', function () {
        return Inertia::render('GanttBryntum');
    })->name('showGantt');

    Route::get('/createSchedule',);

    Route::get('/dataGantt', [GanttController::class, 'get'])->name('dataGantt');

    Route::post('/syncGantt', [GanttController::class, 'sync'])->name('syncGantt');

    Route::post('/syncGanttImporter', [GanttController::class, 'syncImporter'])->name('syncImporter');

    Route::get('/ganttImporter', function () {
        return Inertia::render('GanttImporter');
    })->name('ganttImporter');
    //CRUD Projects
    Route::resource('projects', ProjectController::class);
    // Route::get('create-projects', [ProjectController::class, 'edit'])->name('projects.edit');

    //CRUD Authorizations
    Route::resource('authorizations', AuthorizationController::class);

    //CRUD Quotes
    Route::resource('quotes', QuoteController::class);

    //CRUD Ships
    Route::resource('ships', ShipController::class);

    //CRUD Customers
    Route::resource('customers', CustomerController::class);

    //CRUD Contracts
    Route::resource('contracts', ContractController::class);

});
