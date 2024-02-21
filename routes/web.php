<?php

use App\Events\ContractEvent;
use App\Events\TestWebsocket;
use App\Http\Controllers\Dashboard\DashboardEstimacionesController;
use App\Http\Controllers\DatabaseBackController;
use App\Http\Controllers\Suggestion\SuggestionController;
use App\Ldap\User;
use App\Models\User as UserNotify;
use App\Models\Comment;
use App\Models\Gantt\Task;
use App\Models\Process;
use App\Models\Projects\Customer;
use App\Models\Projects\Project;
use App\Models\Projects\TypeShip;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteStatus;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use App\Models\SWBS\SubSystem;
use App\Models\SWBS\System;
use App\Models\VirtualTask;
use App\Notifications\QuoteAssignmentNotify;
use App\Notifications\QuoteNotify;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Mail\AssignmentToolsMail;
use App\Models\Projects\Contract;
use App\Models\Projects\ProjectsShip;
use App\Models\Projects\Ship;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

Route::get('/', function () {
    return event(new ContractEvent(Contract::first()));
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


        $taskProject = [];

        // VirtualTask::whereNull('task_id')->get()->map(function ($item) {
        //     return [
        //         'id' => $item->id,
        //         'project_id' => $item->project->id,
        //         'avance' => number_format($item['percentDone'], 2),
        //         'name' => $item['name'],
        //         // 'file' => $item->project->contract->ship->file,
        //         'contrato' => $item->project->contract->name,
        //         'duracion' => $item->duration,
        //         'fechaI' => $item->startDate,
        //         'fechaF' => $item->endDate,
        //         'unidadDuracion' => $item->durationUnit,
        //         'costo' => $item->project->cost_sale
        //     ];
        // });

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

    //SUGERENCIAS
    Route::resource('suggestion', SuggestionController::class);

    //NOTIFICACIONES
    Route::get('getNotifications', function () {
        return Inertia::render('Notifications/Index');
    })->name('notifications.index');
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

Route::get('/timeline', [DashboardEstimacionesController::class, 'getQuotesStatus'])->name('timeline');


Route::get('clientes_anterior', function () {
    // QuoteStatus::truncate();
    // QuoteVersion::truncate();
    // QuoteTypeShip::truncate();
    // Comment::truncate();
    // Quote::truncate();
    // Customer::truncate();
    $clientes =  DB::connection('sqlsrv_anterior')->table('clientes')->get();
    foreach ($clientes as $cliente) {
        Customer::create([
            // 'nit' => $cliente->id,
            'name' => $cliente->nombre_cliente,
            'type' => $cliente->tipo_cliente,
            'country' => $cliente->pais,
            'country_en' => strtoupper($cliente->pais),
        ]);
    }
    return Customer::get();
});


Route::get('estmaciones_anterior', function () {
    $estimaciones =  DB::connection('sqlsrv_anterior')->table('estimacions')->get();
    foreach ($estimaciones as $estimacion) {
        $quote = Quote::where('consecutive', $estimacion->consecutivo)->first();
        if (!$quote)
            $quote = Quote::create([
                'gerencia' => auth()->user()->gerencia,
                'name' => $estimacion->nombre,
                'consecutive' => $estimacion->consecutivo,
                'user_id' =>  auth()->user()->id
            ]);

        $cliente = DB::connection('sqlsrv_anterior')->table('clientes')->where('id', $estimacion->cliente_id)->first();
        $cliente_id = null;
        if ($cliente) {
            $cliente_id = Customer::where('name', $cliente->nombre_cliente)->first()->id;
        }
        $quoteVersion = QuoteVersion::FirstOrCreate([
            'quote_id' => $quote->id,
            'version' => $estimacion->version,
            'estimador_id' => $estimacion->estimador_id,
            'customer_id' =>  $cliente_id,
            'expeted_answer_date' => $estimacion->fecha_respuesta_esperada,
            'estimador_anaswer_date' => $estimacion->fecha_respuesta_estimador,
            'offer_type' => $estimacion->tipo_oferta,
            'estimador_name' => $estimacion->nombre_estimador ?? 'Sin Estimador',
            'coin' => $estimacion->moneda_original,
            'file' => $estimacion->file,
        ]);
        $quoteVersion->created_at = $estimacion->fecha_solicitud;
        $quote->current_version_id = $quoteVersion->id;
        $quote->save();
        if ($estimacion->clase_id) {
            QuoteTypeShip::FirstOrCreate([
                'quote_version_id' => $quoteVersion->id,
                'type_ship_id' => $estimacion->clase_id,
                'name' => TypeShip::find($estimacion->clase_id)->name ?? 'Sin Clase',
                'scope' => $estimacion->alcance,
                'maturity' => $estimacion->madurez,
                'margin' => $estimacion->margen_estimado,
                'units' => $estimacion->cantidad,
                'iva' => $estimacion->iva,
                'white_paper' => $estimacion->documento_tecnico,
                'price_before_iva_original' => $estimacion->precio_antes_de_iva_original ?? 0,
            ]);
        }
        $estado = 0;
        $fecha = $estimacion->fecha_solicitud;
        if ($estimacion->firmada) {
            $estado = 3;
            $fecha = $estimacion->fecha_firma;
        } else if ($estimacion->fecha_pendiente_firma != null) {
            $estado = 2;
            $fecha = $estimacion->fecha_pendiente_firma;
        } else if ($estimacion->fecha_respuesta_estimador != null) {
            $estado = 1;
            $fecha = $estimacion->fecha_respuesta_estimador;
        }
        if (isset($quote)) {
            QuoteStatus::create([
                'quote_version_id' => $quoteVersion->id,
                'status' => $estado,
                'fecha' => $fecha,
                'user_id' => auth()->user()->id
            ]);
        }
    }
});
Route::get('clases_anterior', function () {

    $data =  DB::connection('sqlsrv_anterior')->table('clases')->get();
    foreach ($data as $clase) {
        TypeShip::firstOrCreate([
            'name' => $clase->name,
            'type' => $clase->type,
            'disinger' => $clase->empresa_diseñadora,
            'hull_material' => $clase->material_casco,
            'length' => $clase->eslora,
            'breadth' => $clase->manga,
            'draught' => $clase->calado_diseño,
            'depth' => $clase->puntal,
            'full_load' => $clase->full_load,
            'light_ship' => $clase->light_ship,
            'power_total' => $clase->potencia_total_kw,
            'propulsion_type' => $clase->tipo_propulsion,
            'velocity' => $clase->velocidad,
            'autonomy' => $clase->autonomias,
            'crew' => $clase->tripulacion_maxima,
            'GT' => $clase->GT,
            'CGT' => $clase->CGT,
            'bollard_pull' => $clase->bollard_pull,
            'clasification' => $clase->clasificacion,
        ]);
    }
});
Route::get('contratos_anterior', function () {
    // Contract::truncate();
    $data =  DB::connection('sqlsrv_anterior')->table('contratos')->get();
    foreach ($data as $clase) {
        $cliente = DB::connection('sqlsrv_anterior')->table('clientes')->where('id', $clase->cliente_id)->first();
        $id = null;
        if ($cliente) {
            $id = Customer::where('name', $cliente->nombre_cliente)->first()->id ?? null;
        }
        Contract::firstOrCreate([
            'customer_id' => $id,
            'contract_id' => $clase->contrato_id,
            'subject' => $clase->objeto,
            'gerencia' => 'GECON',
            'type_of_sale' => $clase->tipo_venta == 'VENTA FINANCIADA' ? 'FINANCIADA' : $clase->tipo_venta,
            'supervisor' => $clase->supervisor,
            'start_date' => $clase->fecha_inicio,
            'end_date' => $clase->fecha_fin,
            'state' => $clase->estado,
        ]);
    }
});

Route::get('proyectos_anterior', function () {
    // Contract::truncate();
    Ship::truncate();
    $data =  DB::connection('sqlsrv_anterior')->table('proyectos')->get();
    foreach ($data as $proyecto) {


        $clase = DB::connection('sqlsrv_anterior')->table('clases')->where('id', $proyecto->clase_id)->first();
        $contrato = DB::connection('sqlsrv_anterior')->table('contratos')->where('id', $proyecto->contrato_id)->first();
        $contrato_id = null;
        if ($contrato) {
            $contrato_id = Contract::where('contract_id', $contrato->contrato_id)->first()->id ?? null;
        }
        $p = Project::firstOrNew([
            'SAP_code' => $proyecto->codigo_SAP
        ]);
        $p->name = $proyecto->name;
        $p->contract_id = $contrato_id;
        $p->type = $proyecto->tipo_proyecto == 'PROYECTO DE VENTA' ? 'PROYECTO DE VENTA (ARTEFACTO NAVAL)' : ($proyecto->tipo_proyecto == 'PROYECTO DE INVERSIÓN DE BUQUE' ? 'PROYECTO DE INVERSIÓN (ARTEFACTO NAVAL)' : $proyecto->tipo_proyecto);
        $p->status = $proyecto->estado_proyecto == [] ?  null : $proyecto->estado_proyecto;
        $p->scope = $proyecto->alcance == 'CO DESARROLL DISEÑO Y CONSTRUCCIÓN' ?  'CO DESARROLLO DISEÑO Y CONSTRUCCIÓN' : $proyecto->alcance;
        $p->observations = $proyecto->observacions;
        $p->gerencia = 'GECON';
        $p->save();
        $clase_id = null;
        if ($clase) {
            $clase_id = TypeShip::where('name', $clase->name)->first()->id ?? null;
        }

        $s = Ship::firstOrCreate([
            'idHull' => $proyecto->casco,
            'name' => $proyecto->name,
            'acronyms' => $proyecto->siglas_proyecto,
            'type_ship_id' => $clase_id
        ]);
        ProjectsShip::firstOrCreate([
            'project_id' => $p->id,
            'ship_id' => $s->id
        ]);
    }
});

Route::get('peps_anteriores', [DatabaseBackController::class, 'getPep']);
Route::get('grafos-anteriores', [DatabaseBackController::class, 'getGrafos']);
Route::get('operaciones-anteriores', [DatabaseBackController::class, 'getOperations']);
Route::get('hitos-anteriores', [DatabaseBackController::class, 'getHitos']);
Route::get('avance_anteriores', [DatabaseBackController::class, 'getProgress']);


Route::get('/mailable', function () {
    $data = 'Hello';

    return new App\Mail\AssignmentToolsMail($data);
});
Route::post('/prueba', function (Request $request) {

    return back()->withErrors(['errors' => ['messaje', 'default1', 'messaje1', 'default2', 'messaje4', 'default4', 'messaje', 'default',]]);
})->name('prueba');
