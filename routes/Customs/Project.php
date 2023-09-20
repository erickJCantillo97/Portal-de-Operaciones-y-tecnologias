<?php

use App\Http\Controllers\Projects\AuthorizationController;
use App\Http\Controllers\Projects\ContractController;
use App\Http\Controllers\Projects\CustomerController;
use App\Http\Controllers\Projects\ProgrammingController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\QuoteController;
use App\Http\Controllers\Projects\ShipController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/ScheduleNew/{project}', [ScheduleController::class, 'index'])->name('showGantt');

    Route::get('GanttImporter/{project}', [ScheduleController::class, 'import'])->name('ganttImporter');

    Route::get('/ScheduleWizard/{project}', [ScheduleController::class, 'wizard'])->name('wizard');

    Route::get('/ScheduleCreate/{project}', [ScheduleController::class, 'create'])->name('createSchedule.create');

    Route::get('getAssignmentsTask/{task}', [ScheduleController::class, 'getAssignmentsTask'])->name('get.assignments.task');

    Route::post('postExcelImport', [TaskController::class, 'excelImport'])->name('post.excel.import');

    Route::get('/dataGantt/{project}', [ScheduleController::class, 'get'])->name('dataGantt');

    Route::post('/syncGantt/{project}', [ScheduleController::class, 'sync'])->name('syncGantt');

    Route::post('/syncGanttImporter/{project}', [ScheduleController::class, 'syncImporter'])->name('syncImporter');

    //CRUD Projects
    Route::resource('projects', ProjectController::class);

    //CRUD Authorizations
    Route::resource('authorizations', AuthorizationController::class);

    //CRUD Quotes
    Route::resource('quotes', QuoteController::class);

    Route::post('ships/update/{ship}', [ShipController::class, 'update'])->name('ships.update');
    //CRUD Ships
    Route::resource('ships', ShipController::class)->except('update');

    //CRUD Customers
    Route::resource('customers', CustomerController::class);

    //CRUD Contracts
    Route::resource('contracts', ContractController::class);


    Route::get('/programming', [ProgrammingController::class, 'index'])->name('programming');

});
