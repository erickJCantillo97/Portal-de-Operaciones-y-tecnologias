<?php

use App\Http\Controllers\Personal\ProgrammingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/programming', [ProgrammingController::class, 'index'])->name('programming');

    Route::post('/programming/store', [ProgrammingController::class, 'store'])->name('programming.store');

    Route::post('/programming/delete/{schedule}', [ProgrammingController::class, 'deleteSchedule'])->name('programming.delete');

    Route::get('actividadesDeultimonivel', [ProgrammingController::class, 'endNivelActivities'])->name('actividadesDeultimonivel');

    Route::get('scheduleTask', [ProgrammingController::class, 'getScheduleTask'])->name('get.schedule.task');

    Route::post('/programming/saveCustomizedSchedule', [ProgrammingController::class, 'saveCustomizedSchedule'])->name('programming.saveCustomizedSchedule');

});
