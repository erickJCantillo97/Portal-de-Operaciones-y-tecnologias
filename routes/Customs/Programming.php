<?php

use App\Http\Controllers\Projects\ProgrammingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/programming', [ProgrammingController::class, 'index'])->name('programming');
    Route::post('/programming/store', [ProgrammingController::class, 'store'])->name('programming.store');
    Route::delete('/programming/delete', [ProgrammingController::class, 'delete'])->name('programming.delete');
    Route::get('/programming-GEMAM', [ProgrammingController::class, 'indexGEMAM'])->name('programming.index.GEMAM');

    Route::get('actividadesDeultimonivel', [ProgrammingController::class, 'endNivelActivities'])->name('actividadesDeultimonivel');


});
