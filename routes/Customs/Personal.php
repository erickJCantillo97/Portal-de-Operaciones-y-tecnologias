<?php

use App\Http\Controllers\Personal\PersonalController;
use App\Http\Controllers\Personal\ProgrammingController;
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
    Route::get('personalActivos', [PersonalController::class, 'getPersonalActivo'])->name('personal.activos');
    Route::get('updateCargo', function () {
        updateCargos();

        return Labor::get();
    });

    Route::get('getPersonalCargo/', [PersonalController::class, 'getPersonalCargo'])->name('get.personal.cargo');

    Route::get('getCargos', [PersonalController::class, 'getCargos'])->name('get.cargos');
    Route::get('searchPersonal', [PersonalController::class, 'searchPersonal'])->name('search.personal');
    Route::get('getPersonalGerenicaOcifina', [PersonalController::class, 'getPersonalGerenicaOcifina'])->name('get.personal.gerecia.oficia');

    Route::get('getAssignmentHour/{fecha}/{userId}', [ProgrammingController::class, 'getAssignmentHour'])->name('get.assignment.hours');

    Route::get('getTimesEmployee', [ProgrammingController::class, 'getTimesSchedulesEmployee'])->name('get.times.employees');
});
