<?php

use App\Models\Labor;
use Illuminate\Support\Facades\Route;

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('costoPersonal', function(){

        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia);
        $sum = $personal->sum(function ($objeto) {
            return $objeto['Ingresos_Mes'];
        });

        return  $sum;
    });

    Route::get('UpdateCargos', function (){
        UpdateCargos();
        return Labor::get();
    });



});
