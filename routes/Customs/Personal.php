<?php

use App\Http\Controllers\Personal\ContractorEmployeeController;
use App\Http\Controllers\Personal\PersonalController;
use App\Http\Controllers\Personal\ProgrammingController;
use App\Http\Controllers\Personal\TeamController;
use App\Http\Controllers\ProgrammingAdvanceController;
use App\Models\Labor;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('costoPersonal', function () {
        $sum = getPersonalGerenciaOficina(auth()->user()->gerencia)->sum(function ($objeto) {
            return $objeto['Ingresos_Mes'];
        });

        return $sum;
    });
    Route::get('personal', [PersonalController::class, 'index'])->name('personal.index');
    Route::post('personal', [PersonalController::class, 'store'])->name('personal.store');
    Route::get('personalGroups', [PersonalController::class, 'groups'])->name('personal.groups');
    Route::get('personalGroups/{team}', [PersonalController::class, 'personsGroups'])->name('personal.personsGroups');
    Route::delete('personal/delete/{personal}', [PersonalController::class, 'destroy'])->name('personal.destroy');
    Route::get('personalActivos', [PersonalController::class, 'getPersonalActivo'])->name('personal.activos');
    Route::get('personalExport', [PersonalController::class, 'export'])->name('export.personal');

    Route::get('updateCargo', function () {
        updateCargos();
        return Labor::get();
    });

    Route::get('getPersonalCargo/', [PersonalController::class, 'getPersonalCargo'])->name('get.personal.cargo');

    Route::resource('teams', TeamController::class);
    Route::post('addPersonTeam/{team}', [TeamController::class, 'addPersonTeam'])->name('add.person.team');
    Route::post('removePersonTeam/{team}', [TeamController::class, 'removePersonTeam'])->name('remove.person.team');

    Route::get('getCargos', [PersonalController::class, 'getCargos'])->name('get.cargos');
    Route::get('searchPersonal', [PersonalController::class, 'searchPersonal'])->name('search.personal');
    Route::get('getPersonalUser/{id?}', [PersonalController::class, 'getPersonalUser'])->name('get.personal.user');

    Route::get('getAssignmentHour/', [ProgrammingController::class, 'getAssignmentHour'])->name('get.assignment.hours');

    Route::get('getTimesEmployee', [ProgrammingController::class, 'getTimesSchedulesEmployee'])->name('get.times.employees');

    Route::post('getSchedulePersonalStatus', [PersonalController::class, 'getSchedulePersonalStatus'])->name('get.personal.status.programming');

    //Proveedores/Contratistas
    Route::resource('contractorEmployees', ContractorEmployeeController::class);
    
    Route::resource('ProgrammingAdvances', ProgrammingAdvanceController::class);
});
