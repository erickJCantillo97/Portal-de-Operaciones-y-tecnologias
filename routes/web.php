<?php

use App\Events\ContractEvent;
use App\Events\TestWebsocket;
use App\Exports\ProjectsDetailsExport;
use App\Http\Controllers\Dashboard\DashboardEstimacionesController;
use App\Http\Controllers\DatabaseBackController;
use App\Http\Controllers\NotificationUserController;
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
use App\Models\NotificationUser;
use App\Models\Personal\Employee;
use App\Models\Project\ProgressProjectWeek;
use App\Models\Projects\Contract;
use App\Models\Projects\ProjectsShip;
use App\Models\Projects\Ship;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    // $processId = 1;// Asigna un identificador único para cada proceso de carga
    // return event(new ContractEvent($processId));
    if (auth()->user() != null)
        return Inertia::render('Dashboard');
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
        return Inertia::render('Dashboard');
    })->name('dashboard');



    Route::get('get/gerencias', function () {
        return response()->json(['gerencias' => gerencias()]);
    })->name('get.gerencias');




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





Route::get('/timeline', [DashboardEstimacionesController::class, 'getQuotesStatus'])->name('timeline');



Route::get('estmaciones_anterior', function () {
    QuoteStatus::truncate();
    // QuoteTypeShip::forceDeleted();
    QuoteVersion::truncate();
    Quote::truncate();
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
            $cliente_id = Customer::where('name', $cliente->nombre_cliente)->first()->id ?? null;
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
            $class =  DB::connection('sqlsrv_anterior')->table('clases')->where('id', $estimacion->clase_id)->first();
            $clase = TypeShip::where('name', $class->name)->first();
            $quoteShip = QuoteTypeShip::FirstOrCreate([
                'quote_version_id' => $quoteVersion->id,
                'type_ship_id' => $clase->id ?? 0,
                'name' => $clase->name ?? 'Sin Clase',
                'scope' => $estimacion->alcance,
                'maturity' => $estimacion->madurez,
                'margin' => $estimacion->margen_estimado,
                'units' => $estimacion->cantidad,
                'iva' => $estimacion->iva,
                'white_paper' => $estimacion->documento_tecnico,
                'price_before_iva_original' => $estimacion->precio_antes_de_iva_original ?? 0,
            ]);
            if ($clase) {
                $quoteShip->type_ship_id = $clase->id;
                $quoteShip->name = $clase->name ?? $quoteShip->name;
                $quoteShip->save();
            }
        }
        $estado = 0;
        $fecha = $estimacion->fecha_solicitud;
        if ($estimacion->firmada) {
            $estado = 3;
            $fecha = $estimacion->fecha_firma;
        } else if ($estimacion->fecha_pendiente_firma != null) {
            $estado = 2;
            $fecha = $estimacion->fecha_pendiente_firma ?? '2023-07-18';
        } else if ($estimacion->fecha_respuesta_estimador != null) {
            $estado = 1;
            $fecha = $estimacion->fecha_respuesta_estimador;
        }
        if (isset($quote) && isset($fecha)) {
            QuoteStatus::create([
                'quote_version_id' => $quoteVersion->id,
                'status' => $estado,
                'fecha' => $fecha,
                'user_id' => auth()->user()->id
            ]);
        }
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
Route::get('/prueba', function (Request $request) {
    setEmpleadosAPI();
    return Employee::get();
    // return back()->withErrors(['errors' => ['messaje', 'default1', 'messaje1', 'default2', 'messaje4', 'default4', 'messaje', 'default',]]);

    // return Excel::download(new ProjectsDetailsExport, 'invoices.xlsx');
})->name('prueba');


Route::get('add-super-user', function () {
    $user = UserNotify::find(23)->assignRole('Super Admin');
});

Route::get('getNotifications', [NotificationUserController::class, 'index'])->name('get.notifications');
