<?php

use App\Events\TestWebsocket;
use App\Http\Controllers\Suggestion\SuggestionController;
use App\Ldap\User;
use App\Models\Gantt\Task;
use App\Models\Process;
use App\Models\Projects\Project;
use App\Models\SWBS\SubSystem;
use App\Models\SWBS\System;
use App\Models\VirtualTask;
use Carbon\Carbon;
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

    Route::post('foto/{correo}', function ($correo) {
        return response()->json([
            'photo' => User::where('userprincipalname', $correo)->first()->photo(),
        ]);
    })->name('get.foto');

    Route::get('/dashboard', function () {
        $taskProject = VirtualTask::whereNull('task_id')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'project_id' => $item->project->id,
                'avance' => number_format($item['percentDone'], 2),
                'name' => $item['name'],
                // 'file' => $item->project->contract->ship->file,
                'contrato' => $item->project->contract->name,
                'duracion' => $item->duration,
                'fechaI' => $item->startDate,
                'fechaF' => $item->endDate,
                'unidadDuracion' => $item->durationUnit,
                'costo' => $item->project->contract->cost
            ];
        });

        return Inertia::render('Dashboard', [
            'projects' => $taskProject,
        ]);
    })->name('dashboard');

    Route::get('getEmpleadosGerencias', function () {
        if (auth()->user()->hasRole('Super Admin')) {
            return getEmpleadosAPI()->groupBy('GERENCIA');
        }

        return searchEmpleados('Gerencia', auth()->user()->gerencia)->groupBy('Oficina');
    })->name('get.empleados.gerencia');

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

    Route::resource('suggestion', SuggestionController::class);
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
    $datos = DB::connection('sqlsrv_anterior')->table('swbs_process')->select(['subsystem_id', 'validity', 'status', 'name', 'maintenance_type'])->get();
    foreach ($datos as $dato) {
        Process::create((array) $dato);
    }

    return System::get();
});

Route::get('projectAvance', function () {
    $proyecto = Project::first();

    $taskProject = Task::where('project_id', $proyecto->id)->whereNull('task_id')->get()->map(function (Task $item) {
        return [
            number_format($item['percentDone'], 2),
            $item['name'],
        ];
    });

    return $taskProject;
});

Route::get('costoPersonal', function () {

    $personal = getPersonalGerenciaOficina(auth()->user()->gerencia);
    $sum = $personal->sum(function ($objeto) {
        return $objeto['Ingresos_Mes'];
    });

    return $sum;
});

Route::get('/timeline', function () {

    $taskProject = VirtualTask::whereNull('task_id')->get()->map(function ($item) {

        return [
            'id' => $item->id,
            'project_id' => $item->project->id,
            'avance' => number_format($item['percentDone'], 2),
            'name' => $item['name'],
            'file' => $item->project->contract->ship->file,
            'duracion' => $item->duration,
            'fechaI' => $item->startDate,
            'fechaF' => $item->endDate,
            'unidadDuracion' => $item->durationUnit,
        ];
    });

    return Inertia::render('TimeLine', [
        'projects' => $taskProject,
    ]);
})->name('timeline');
