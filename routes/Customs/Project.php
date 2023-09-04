<?php

use App\Http\Controllers\Projects\GanttController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'),'verified',])->group(function () {

    Route::get('/cronograma', function () {
        return Inertia::render('GanttBryntum');
    })->name('showGantt');

    Route::get('/dataGantt', [GanttController::class, 'get'])->name('dataGantt');

    Route::post('/syncGantt', [GanttController::class, 'sync'])->name('syncGantt');

    Route::post('/syncGanttImporter', [GanttController::class, 'syncImporter'])->name('syncImporter');

    Route::get('/ganttImporter', function () {
        return Inertia::render('GanttImporter');
    })->name('ganttImporter');

});
