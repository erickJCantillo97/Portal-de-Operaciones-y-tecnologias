<?php

use App\Http\Controllers\GD\ManagerDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('getTipologias', function () {

    $tipologias =  GDTipologias()->filter(function ($t) {
        // $t['Dependencia'] == auth()->user()->gerencia &&
        return ($t['idsubserie'] == 197 || $t['idsubserie'] == 201);
    });

    return response()->json([
        'tipologias' => $tipologias
    ]);
})->name('get.tipologias');


Route::post('gestionDocumentalStore', [ManagerDocumentController::class, 'store'])->name('gestion.documental.store');
