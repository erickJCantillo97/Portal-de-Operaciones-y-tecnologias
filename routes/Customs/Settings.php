<?php


use App\Http\Controllers\SWBS\BaseActivityController;
use App\Http\Controllers\SWBS\ProcessController;
use App\Http\Controllers\SWBS\SpecificActivityController;
use App\Http\Controllers\SWBS\SubSystemController;
use App\Http\Controllers\SWBS\SystemController;
use App\Http\Controllers\Personal\ShiftController;
use App\Http\Controllers\Suggestion\SuggestionController;
use App\Models\Suggestion;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('', function () {
        return Inertia::render('Settings/Basic/Index');
    })->name('settings.basic');

    Route::resource('baseActivities', BaseActivityController::class);
    Route::resource('specificActivities', SpecificActivityController::class);
    Route::resource('system', SystemController::class);
    Route::resource('subsystem', SubSystemController::class);
    Route::resource('process', ProcessController::class);

    Route::get('gerencias/get', function () {
        return response()->json([gerencias()], 200);
    })->name('gerencias.index');

    Route::get('gruposConstructivos/get', function () {
        return response()->json([gruposConstructivos()], 200);
    })->name('gruposConstructivos.index');

    Route::resource('shift', ShiftController::class);
    Route::resource('suggestion', SuggestionController::class);

});
