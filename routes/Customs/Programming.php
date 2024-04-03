<?php

use App\Http\Controllers\Personal\ProgrammingController;
use App\Http\Controllers\Personal\ExtendedScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/programming', [ProgrammingController::class, 'index'])->name('programming');

    Route::get('/programming/create', [ProgrammingController::class, 'create'])->name('programming.create');

    Route::post('/programming/store', [ProgrammingController::class, 'store'])->name('programming.store');

    Route::post('/programming/delete/{schedule}', [ProgrammingController::class, 'deleteSchedule'])->name('programming.delete');

    Route::get('actividadesDeultimonivel', [ProgrammingController::class, 'endNivelActivities'])->name('actividadesDeultimonivel');

    Route::get('scheduleTask', [ProgrammingController::class, 'getScheduleTask'])->name('get.schedule.task');

    Route::post('/programming/saveCustomizedSchedule', [ProgrammingController::class, 'saveCustomizedSchedule'])->name('programming.saveCustomizedSchedule');

    Route::post('/programming/collisionsPerDay', [ProgrammingController::class, 'collisionsPerDay'])->name('programming.collisionsPerDay');

    Route::post('/programming/removeSchedule', [ProgrammingController::class, 'removeSchedule'])->name('programming.removeSchedule');

    Route::get('/gettaskdatedivision', [ProgrammingController::class, 'getTaskDateDivision'])->name('get.task.date.division');

    Route::get('actividadesDeultimonivelPorProyectos/{project}', [ProgrammingController::class, 'endNivelActivitiesByProject'])->name('actividadesDeultimonivelPorProyectos');


    

    /********************************  Rutas del controlador ExtendedScheduleController  ******************************/

    Route::post('/ExtendedSchedule/store', [ExtendedScheduleController::class, 'store'])->name('ExtendedSchedule.store');

    Route::post('/ExtendedSchedule/update', [ExtendedScheduleController::class, 'update'])->name('ExtendedSchedule.update');

    Route::get('/ExtendedSchedule/getTask/{project}', [ExtendedScheduleController::class, 'getTask'])->name('ExtendedSchedule.getTask');

    Route::get('/ExtendedSchedule/all', [ExtendedScheduleController::class, 'all'])->name('ExtendedSchedule.all');

    Route::delete('/ExtendedSchedule/destroy/{id}', [ExtendedScheduleController::class, 'destroy'])->name('ExtendedSchedule.destroy');


});
