<?php

use App\Models\Gantt\Task;
use App\Models\Process;
use App\Models\SWBS\SubSystem;
use App\Models\SWBS\System;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        //return ModelToolsAterior::get();
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('pruebaApi', function () {
        if (auth()->user()->hasRole('Super Admin')) {
            return getEmpleadosAPI()->groupBy('GERENCIA');
        }

        return searchEmpleados('GERENCIA', auth()->user()->gerencia)->groupBy('OFICINA');
    })->name('pruebaApi');

    Route::get('simple/crud', function (Request $request) {
        $request = $request->all();
    })->name('simple.crud');

    Route::get('get/gerencias', function () {
        return response()->json(['gerencias' => gerencias()]);
    })->name('get.gerencias');

    Route::get('dashboard/{gerencia}', function ($gerencia) {
        $personal = searchEmpleados('GERENCIA', $gerencia)->groupBy('OFICINA');

        return Inertia::render('Dashboards/Gerencias', ['personal' => $personal, 'GERENCIA' => $gerencia]);
    })->name('dashboard.gerencias');

    Route::get('crearTarea', function () {
        Task::truncate();
        Task::create([
            'name' => 'ARC PUNTA ESPADA',
            'percentDone' => 50,
            'duration' => 260,
            'startDate' => Carbon::now(),
            'endDate' => Carbon::now()->addDays(260),
        ]);
    });

});

Route::get('recuperarDatos', function () {

    $datos = DB::connection('sqlsrv_anterior')->table('swbs_systems')->select(['code', 'validity', 'status', 'name', 'constructive_group_id'])->get();

    foreach ($datos as $dato) {
        System::create((array) $dato);
    }

    $datos = DB::connection('sqlsrv_anterior')->table('swbs_subsystems')->select(['system_id', 'code', 'validity', 'status', 'name'])->get();
    foreach ($datos as $dato) {
        SubSystem::create((array) $dato);
    }
    $datos = DB::connection('sqlsrv_anterior')->table('swbs_process')->select(['subsystem_id', 'validity', 'status','name', 'maintenance_type'])->get();
    foreach ($datos as $dato) {
        Process::create((array) $dato);
    }

    return System::get();
});

Route::get('actividadesDeultimonivel', function () {
        $date = Carbon::now();
        $tareas =  Task::whereNotNull('task_id')->select('task_id')->get()->toArray();

        $ids = array_map(function ($objeto) {
            return $objeto['task_id'];
        }, $tareas);

        return response()->json(
            Task::where('startDate', '<=', $date)->where('endDate', '>=', $date)->whereNotIn('id', array_unique($ids))->get()
        );
});
