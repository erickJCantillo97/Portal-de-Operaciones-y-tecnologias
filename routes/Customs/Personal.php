<?php

use App\Http\Controllers\Personal\PersonalController;
use App\Models\Labor;
use Illuminate\Support\Facades\Route;

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

        Route::get('costoPersonal', function(){
            $sum = getPersonalGerenciaOficina(auth()->user()->gerencia)->sum(function ($objeto) {
                return $objeto['Ingresos_Mes'];
            });
            return  $sum;
        });

        Route::get('updateCargo', function (){
            updateCargos();
            return Labor::get();
        });

        Route::get('getPersonalCargo/', [PersonalController::class, 'getPersonalCargo'])->name('get.personal.cargo');


        Route::get('getCargos', [personalController::class, 'getCargos'])->name('get.cargos');

        Route::get('getPersonal', [personalController::class, 'getPersonal'])->name('get.personal');
});
