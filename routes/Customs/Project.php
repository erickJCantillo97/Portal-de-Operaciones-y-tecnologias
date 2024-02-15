<?php

use App\Http\Controllers\ProgressProjectWeekController;
use App\Http\Controllers\Projects\AuthorizationController;
use App\Http\Controllers\Projects\Budget\BudgetController;
use App\Http\Controllers\Projects\ContractController;
use App\Http\Controllers\Projects\CustomerController;
use App\Http\Controllers\Projects\MilestoneController;
use App\Http\Controllers\Projects\ProgrammingController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Projects\QuoteController;
use App\Http\Controllers\Projects\ShipController;
use App\Http\Controllers\Projects\TypeShipController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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
    Route::resource('ProgressProjectWeek', ProgressProjectWeekController::class);
    Route::get('project-overview/{project}', [ProjectController::class, 'goToProjectOverview'])->name('projects.goToProjectOverview');

    Route::post('projects/addShips/{project}', [ProjectController::class, 'addShips'])->name('project.add.ships');

    Route::resource('tasks', TaskController::class);

    Route::post('typeShips/{project}/update', [TypeShipController::class, 'update'])->name('typeShips.update');


    Route::resource('typeShips', TypeShipController::class)->except('update');
    Route::get('typeShips/getProject/{typeShip}', [TypeShipController::class, 'getProject'])->name('typeship.get.project');
    //CRUD Authorizations
    Route::resource('authorizations', AuthorizationController::class);



    Route::post('ships/update/{ship}', [ShipController::class, 'update'])->name('ships.update');
    //CRUD Ships
    Route::resource('ships', ShipController::class)->except('update');

    Route::get('budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('getDetailsBudget/{project}', [BudgetController::class, 'getDetailsBudget'])->name('get.details.budget');

    //CRUD Customers
    Route::resource('customers', CustomerController::class);

    //CRUD Contracts
    Route::resource('contracts', ContractController::class);
    //JSON Contracts
    Route::get('getContracts', [ContractController::class, 'getContracts'])->name('getContracts');

    //CRUD Programming

    /// CRUD HITOS

    Route::resource('milestones', MilestoneController::class);
});
