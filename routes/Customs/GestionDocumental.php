<?php

use App\Http\Controllers\GD\ManagerDocumentController;
use Illuminate\Support\Facades\Route;

Route::get('getTipologias', [ManagerDocumentController::class, 'index'])->name('get.tipologias');


Route::post('gestionDocumentalStore', [ManagerDocumentController::class, 'store'])->name('gestion.documental.store');
