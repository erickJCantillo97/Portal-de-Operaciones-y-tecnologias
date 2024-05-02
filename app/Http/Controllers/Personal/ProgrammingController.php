<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Jobs\Personal\PersonalScheduleDayJob;
use App\Ldap\User;
use App\Models\Gantt\Assignment;
use App\Models\Personal\ContractorEmployee;
use App\Models\Personal\Employee;
use App\Models\Personal\ExtendedSchedule;
use App\Models\Projects\Project;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\Shift;
use App\Models\Views\DetailScheduleTime;
use App\Models\VirtualTask;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProgrammingController extends Controller
{
    public function index()
    {
        $projects = Project::active()->get();

        // Obtener la fecha actual
        $currentDate = Carbon::now();

        // Obtener el inicio de la próxima semana (lunes)
        $nextMonday = $currentDate->next()->startOfWeek();

        // Si hoy es lunes, obtenemos el siguiente lunes
        if ($currentDate->isMonday()) {
            $nextMonday = $nextMonday->addWeek();
        }

        foreach ($projects as $key => $project) {
            $extendedTask = ExtendedSchedule::with('task')->where('project_id', $project->id)->whereBetween('date', [Carbon::yesterday(), $nextMonday])->get();

            $project->tasks = $extendedTask;
        }

        return Inertia::render('Programming/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $projects = Project::active()->get()->map(function ($project) {
            $task = VirtualTask::where('project_id', $project->id)->whereNull('task_id')->first();
            return [
                'name' => $project->name,
                'id' => $project->id,
                'fechaI' => $task->startDate ?? '',
                'fechaF' => $task->endDate ?? '',
                'avance' => $task->percentDone ?? 0,
            ];
        });


        return Inertia::render('Programming/Create', [
            'projects' => $projects,
        ]);
    }

    public function getProgrammingDate(Request $request)
    {
        $date = Carbon::parse($request->date);
        $programmin = DetailScheduleTime::where('fecha', $date->format('Y-m-d'))
            ->orderBy('idproyecto')
            ->orderBy('idTask')
            ->get()->map(function ($d) {
                return [
                    'task' => $d['nombreTask'],
                    'project' => $d['NombreProyecto'],
                    'user' => $d['nombre'],
                    'cargo' => $d['Cargo'],
                    'division' => $d['Oficina'],
                    'turno' => Carbon::createFromFormat('H:i:s', substr($d['horaInicio'], 0, 8))->format('g:i') . ' - ' . Carbon::createFromFormat('H:i:s', substr($d['horaFin'], 0, 8))->format('g:i'),
                ];
            });
        return response()->json(
            [
                'programming' =>  $programmin
            ],
            200
        );
    }

    /**
     * La función almacena una programación y crea un horario para una tarea y luego devuelve la
     * información de la tarea junto con la programación.
     *
     * @param {Request} request - El parámetro `` es una instancia de la clase
     * `Illuminate\Http\Request`. Representa la solicitud HTTP realizada al servidor y contiene
     *
     * @returns una respuesta JSON con un estado y datos de la tarea.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $status = false;
        $conflict = [];
        try {
            $validateData = $request->validate([
                'task_id' => 'required',
                'employee_id' => 'required',
                'name' => 'required',
                'fecha' => 'required|date', //fecha seleccionada del calendario
            ]);
            $date = Carbon::parse($validateData['fecha']);
            $task = VirtualTask::find($validateData['task_id']);
            $exist = DetailScheduleTime::where('idUsuario', $validateData['employee_id'])
                ->where('fecha', $date->format('Y-m-d'))
                ->where('idTask', '!=', $validateData['task_id'])
                ->get();
            if ($exist->count() > 0) {
                // se agrega el task a las actividades que generan conflictos.
                $exist = collect($exist)->each(function ($DetailScheduleTime) {
                    $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                });
                $conflict[$date->format('Y-m-d')] = $exist;
                $status = false;
            } else {
                $employee = Employee::where('Num_SAP', 'LIKE', '%' . $validateData['employee_id'])->first();
                $schedule = Schedule::firstOrNew([
                    'task_id' => $validateData['task_id'],
                    'employee_id' => $validateData['employee_id'],
                    'name' => $validateData['name'],
                    'fecha' => $date->format('Y-m-d'),
                ]);
                $schedule->save();

                ScheduleTime::firstOrCreate([
                    'schedule_id' => $schedule->id,
                    'hora_inicio' => Carbon::parse($task->project->shiftObject->startShift)->format('H:i'),
                    'hora_fin' => Carbon::parse($task->project->shiftObject->endShift)->format('H:i'),
                ]);
                /* adicional de guardar en schedule y scheduleTime, se guarda en la tabla Assignment para 
                que pueda aparecer en los recursos del gantt */
                Assignment::firstOrCreate([
                    'event' => $validateData['task_id'],
                    'resource' => $validateData['employee_id'],
                    'units' => 100,
                    'name' => $validateData['name'],
                    'costo_hora' => $employee->Costo_Hora,
                ]);
                $status = true;
            }
            DB::commit();

            return response()->json([
                'status' => $status,
                'task' => $this->getSchedule($validateData['fecha'], $validateData['task_id']),
                'conflict' => $conflict,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function update(ScheduleTime $scheduleTime, Request $request)
    {

        $scheduleTime->update([
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);

        return response()->json([
            'status' => true,
            'task' => $this->getSchedule($scheduleTime->schedule->fecha, $scheduleTime->schedule->task_id),
            'hours' => $this->getAssignmentHour($scheduleTime->schedule->fecha, $scheduleTime->schedule->employee_id),
        ], 200);
    }

    public function deleteSchedule(Schedule $schedule)
    {
        $schedule->delete();
        ScheduleTime::where('schedule_id', $schedule->id)->delete();

        return response()->json([
            'status' => true,
            'task' => $this->getSchedule($schedule->fecha, $schedule->task_id),
            'hours' => $this->getAssignmentHour($schedule->fecha, $schedule->employee_id),
        ], 200);
    }

    public function getTaskDateDivision(Request $request)
    {
        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();
        $date = Carbon::parse($request->date);

        return response()->json(
            VirtualTask::has('project')->has('task')
                ->where('startDate', '<=', $date)
                ->where('percentDone', '<', 100)
                ->where('executor', 'like', '%' . $request->division . '%')
                ->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task) {
                    return [
                        'name' => $task['name'],
                        'id' => $task['id'],
                        'process' => $task->task->name,
                        'endDate' => $task['endDate'],
                        'percentDone' => $task['percentDone'],
                        'project' => $task->project->name,
                        'startDate' => $task['startDate'],
                    ];
                }),
        );
    }

    /**
     * La función `endNivelActivities` recupera una lista de tareas virtuales que finalizaron dentro de
     * un rango de fechas específico, excluyendo las tareas asociadas con un ID de tarea.
     *
     * @param {Request} request - El parámetro `` es una instancia de la clase
     * `Illuminate\Http\Request`. Representa la solicitud HTTP realizada al servidor y contiene diversa
     * información, como parámetros de solicitud, encabezados y el cuerpo de la solicitud.
     *
     * @returns una respuesta JSON. La respuesta contiene una colección de objetos VirtualTask que
     * cumplen determinadas condiciones. Cada objeto VirtualTask se transforma en una matriz con las
     * siguientes propiedades: 'nombre', 'id', 'endDate', 'percentDone', 'project' y 'startDate'.
     */
    public function endNivelActivities(Request $request)
    {
        if (isset($request->dates[0])) {
            $date_start = Carbon::parse($request->dates[0])->format('Y-m-d');
            $date_end = Carbon::parse($request->dates[1])->format('Y-m-d');
        } elseif (isset($request->date)) {
            $date_start = Carbon::parse($request->date)->format('Y-m-d');
            $date_end = Carbon::parse($request->date)->format('Y-m-d');
        } else {
            $date_start = Carbon::tomorrow()->format('Y-m-d');
            $date_end = Carbon::tomorrow()->format('Y-m-d');
        }

        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();

        return response()->json(
            VirtualTask::has('project')->has('task')->where(function ($query) use ($date_start, $date_end) {
                $query->whereBetween('startdate', [$date_start, $date_end])
                    ->orWhereBetween('enddate', [$date_start, $date_end])
                    ->orWhere(function ($query) use ($date_start, $date_end) {
                        $query->where('enddate', '>', $date_end)
                            ->where('startdate', '<', $date_start);
                    });
            })->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task) {
                return [
                    'name' => $task['name'],
                    'id' => $task['id'],
                    'task' => $task->task->name,
                    'endDate' => $task['endDate'],
                    'percentDone' => $task['percentDone'],
                    'project' => $task->project->name,
                    'shift' => $task->project->shift ? Shift::where('id', $task->project->shift)->first() : null,
                    'startDate' => $task['startDate'],
                ];
            }),
        );
    }

    /**
     * La función recupera una programación para una tarea específica en una fecha determinada y la
     * devuelve como una respuesta JSON.
     *
     * @param {Request} request - El parámetro  es una instancia de la clase Request, que se
     * utiliza para recuperar datos de la solicitud HTTP. Contiene información como el método de
     * solicitud, encabezados y datos de entrada.
     *
     * @returns una respuesta JSON con la variable 'programación', que contiene los datos de
     * programación recuperados de la base de datos.
     */
    public function getScheduleTask(Request $request)
    {
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $schedule = Schedule::where('fecha', $date)->with('scheduleTimes')->where('task_id', $request->task_id)->get()->sortByDesc([
            ['is_my_personal', 'desc'],
        ]);

        return response()->json([
            'schedule' => $schedule,
        ], 200);
    }

    public function getSchedule($fecha, $task)
    {

        return DetailScheduleTime::groupBy('idUsuario')->where('idTask', $task)->where('fecha', $fecha)->select(
            Db::raw('MIN(nombre) as name'),
            Db::raw('MIN(idUsuario) as id'),
            Db::raw('MIN(idSchedule) as schedule'),
        )->get()->map(function ($d) use ($task, $fecha) {
            return [
                'Nombres_Apellidos' => $d->name,
                'Num_SAP' => $d->id,
                'schedule' => $d->schedule,
                'times' => DetailScheduleTime::where([
                    ['fecha', '=', $fecha],
                    ['idTask', '=', $task],
                    ['idUsuario', '=', $d->id],
                ])->get(),
                'photo' =>  User::where('employeenumber',  str_pad(
                    $d->id,
                    8,
                    '0',
                    STR_PAD_LEFT
                ))->first()->photo(),
            ];
        });
        // return Schedule::where('fecha', $fecha)->with('scheduleTimes')->where('task_id', $taskId)->get()->sortBy([
        //     ['is_my_personal', 'desc'],
        // ]);
    }

    /*
     * Esta función obtiene el numero de horas asignadas a una persona en una fecha especifica
     */
    public function getAssignmentHour($fecha, $userId)
    {
        $schedule = Schedule::where([
            'fecha' => $fecha,
            'employee_id' => $userId,
        ])->pluck('id')->toArray();

        $horas_acumulados = DetailScheduleTime::whereIn('idSchedule', $schedule)->selectRaw('SUM(datediff(mi,horaInicio, horaFin)) as diferencia_acumulada')->get();

        // dd($horas_acumulados);
        return $horas_acumulados[0]->diferencia_acumulada / 60;
    }

    public function getTimesSchedulesEmployee(Request $request)
    {
        // Obtener la fecha actual
        $friday = Carbon::now()->endOfWeek()->subDays(2);


        $employee = $request->employee_id ?? auth()->user()->num_sap;
        $date = Carbon::parse($request->date ?? Carbon::now())->format('Y-m-d');

        $times = DetailScheduleTime::whereBetween('fecha', [Carbon::now(), $friday])->orWhere('fecha', $date)->where('idUsuario', $employee)->get()->map(function ($time) use ($date) {
            return [
                'id' => $time['idScheduleTime'],
                'start' => Carbon::parse($time['horaInicio'])->format('H:i'),
                'end' => Carbon::parse($time['horaFin'])->format('H:i:'),
                'title' => $time['nombreTask'],
                'date' => $time['fecha'],
                'project' => $time['NombreProyecto'],
            ];
        });


        return response()->json([
            'times' => $times,
        ]);
    }

    public function saveCustomizedSchedule(Request $request)
    {
        try {
            DB::beginTransaction();
            $conflict = [];
            $detailScheduleTime = DetailScheduleTime::where('idScheduleTime', $request->schedule_time)->first();
            $task = VirtualTask::find(Schedule::find($detailScheduleTime->idSchedule)->task_id);
            $project = Project::find($task->project_id);
            if ($request->personalized) {
                Shift::firstOrCreate([
                    'name' => $request->timeName,
                    'startShift' => new DateTime('1970-01-01T' . $request->startShift . ':00'),
                    'endShift' => new DateTime('1970-01-01T' . $request->endShift . ':00'),
                    'timeBreak' => $request->timeBreak,
                    'status' => false,
                    'user' => auth()->user()->num_sap
                ]);
            }
            $exist = DetailScheduleTime::where('fecha', $detailScheduleTime->fecha)
                ->where('idSchedule', '!=', $detailScheduleTime->idSchedule)
                ->where('idUsuario', $detailScheduleTime->idUsuario)
                ->where(function ($query) use ($request) {
                    $query->where(function ($subquery) use ($request) {
                        $subquery->whereTime('horaInicio', '<=', $request->startShift)
                            ->whereTime('horaFin', '>=', $request->startShift);
                    })->orWhere(function ($subquery) use ($request) {
                        $subquery->whereTime('horaInicio', '<=', $request->endShift)
                            ->whereTime('horaFin', '>=', $request->endShift);
                    })->orWhere(function ($subquery) use ($request) {
                        $subquery->whereBetween('horaInicio', [$request->startShift, $request->endShift])
                            ->whereBetween('horaFin', [$request->startShift, $request->endShift]);
                    });
                })->get();
            if ($exist->count() > 0) {
                $exist = $exist->each(function ($DetailScheduleTime) {
                    $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                });
                $conflict[$request->date] = $exist;
            } else {
                if (getWorkingDays($detailScheduleTime->fecha, $project->daysPerWeek)) {
                    $ScheduleTime = ScheduleTime::find($request->schedule_time);
                    $ScheduleTime->hora_inicio = $request->startShift;
                    $ScheduleTime->hora_fin = $request->endShift;
                    $ScheduleTime->save();
                }
            }
            DB::commit();
            if (count($conflict) > 0) {
                return response()->json(['status' => false, 'conflict' => $conflict, 'mensaje' => '', 'task' => $this->getSchedule($detailScheduleTime->fecha, $task->id)]);
            }
            return response()->json(['status' => true, 'conflict' => [], 'mensaje' => 'Horario asignado', 'task' => $this->getSchedule($detailScheduleTime->fecha, $task->id)]);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function collisionsPerDay(Request $request)
    {
        try {
            // $request->endSchedule = true;
            // $request->actionType = 2;
            // $request->scheduleTime = 333;
            // $request->idTask = 18;
            DB::beginTransaction();
            $detailScheduleTime = DetailScheduleTime::where('idScheduleTime', $request->scheduleTime)->first();
            //return $detailScheduleTime;
            $mensaje = '';
            switch ($request->actionType) {
                case 2:
                    //ACCIÓN REEMPLAZAR
                    $schedule = Schedule::find($detailScheduleTime->idSchedule);
                    $schedule->task_id = $request->idTask;
                    $schedule->save();
                    if ($request->endSchedule) {
                        PersonalScheduleDayJob::dispatch($detailScheduleTime->fecha, $detailScheduleTime->idUsuario, $request->idTask)->onConnection('sync');
                        $mensaje = 'registro actualizado';
                    }
                    break;
                case 1:
                    //ACCIÓN OMITIR
                    if ($request->endSchedule) {
                        PersonalScheduleDayJob::dispatch($detailScheduleTime->fecha, $detailScheduleTime->idUsuario, $request->idTask)->onConnection('sync');
                        $mensaje = 'registro actualizado';
                    }
                    break;
                default:
                    break;
            }
            DB::commit();
            return response()->json(
                [
                    'status' => true,
                    'mensaje' => $mensaje,
                    'task' => $request->actionType == 1 ?
                        $this->getSchedule($detailScheduleTime->fecha, $detailScheduleTime->idTask) :
                        $this->getSchedule($schedule->fecha, $request->idTask)
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => true, 'mensaje' => 'Se ha generado un error: ' . $e->getMessage()]);
        }
    }

    public function removeSchedule(Request $request)
    {
        try {
            DB::beginTransaction();
            $schedule = Schedule::find($request->schedule);
            //se eliminan el recurso en el gantt
            Assignment::where('event', $schedule->task_id)
                ->where('resource', $schedule->employee_id)->delete();
            ScheduleTime::where('schedule_id', $schedule->id)->delete();
            $schedule->delete();
            DB::commit();

            return response()->json([
                'status' => true,
                'mensaje' => 'Horario eliminado',
                'task' => $this->getSchedule($schedule->fecha, $schedule->task_id),
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return $e;
        }
    }

    public function endNivelActivitiesByProject(Project $project, Request $request)
    {
        // $request->date_end ='2024-01-01';
        // $request->date_start = '2024-05-05';
        // $request->idProject = 1;
        // dd($request->all());
        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();

        $date = Carbon::parse($request->date);

        if ($date->isSunday()) {
            return response()->json(
                ExtendedSchedule::has('project')->has('task')->where('project_id', $project->id)
                    // ->where('executor', 'LIKE', '%' . auth()->user()->oficina . '%')
                    ->where('date', $date)
                    ->get()->map(function ($task) use ($date) {
                        return [
                            'name' => $task['task']['name'],
                            'id' => $task['id'],
                            'task' => $task->task->task->name,
                            'taskdad' => $task->task->task->name ?? '',
                            'endDate' => $task['task']['endDate'],
                            'startDate' => $task['task']['startDate'],
                            'percentDone' => $task['task']['percentDone'],
                            'shift' => $task->project->shift ? Shift::where('id', $task->project->shift)->first() : null,
                            'employees' => DetailScheduleTime::groupBy('idUsuario')->where('idTask', $task['task']['id'])->where('fecha', $date)->select(
                                Db::raw('MIN(nombre) as name'),
                                Db::raw('MIN(idUsuario) as id'),
                                Db::raw('MIN(idSchedule) as schedule'),
                            )->get()->map(function ($d) use ($task, $date) {
                                return [
                                    'name' => $d->name,
                                    'user_id' => $d->id,
                                    'schedule' => $d->schedule,
                                    'times' => DetailScheduleTime::where([
                                        ['fecha', '=', $date],
                                        ['idTask', '=', $task['id']],
                                        ['idUsuario', '=', $d->id],
                                    ])->get(),
                                    'photo' =>  User::where('employeenumber',  str_pad(
                                        $d->id,
                                        8,
                                        '0',
                                        STR_PAD_LEFT
                                    ))->first()->photo(),
                                ];
                            }),
                        ];
                    })
            );
        }

        return response()->json(
            VirtualTask::has('project')->has('task')
                ->where('project_id', $project->id)
                // ->where('executor', 'LIKE', '%' . auth()->user()->oficina . '%')
                ->where('percentDone', '<', 100)
                ->where('startDate', '<=', $date)
                // ->where(function ($query) use ($request) {
                //     $query->whereBetween('startdate', [$request->date_start, $request->date_end])
                //         ->orWhereBetween('enddate', [$request->date_start, $request->date_end])
                //         ->orWhere(function ($query) use ($request) {
                //             $query->where('enddate', '>', $request->date_end)
                //                 ->where('startdate', '<', $request->date_start);
                //         });
                ->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task) use ($date) {
                    return [
                        'name' => $task['name'],
                        'id' => $task['id'],
                        'task' => $task->task->name,
                        'taskdad' => $task->task->task->name ?? '',
                        'endDate' => $task['endDate'],
                        'startDate' => $task['startDate'],
                        'percentDone' => $task['percentDone'],
                        'shift' => $task->project->shift ? Shift::where('id', $task->project->shift)->first() : null,
                        'employees' => DetailScheduleTime::groupBy('idUsuario')->where('idTask', $task['id'])->where('fecha', $date)->select(
                            Db::raw('MIN(nombre) as name'),
                            Db::raw('MIN(idUsuario) as id'),
                            Db::raw('MIN(idSchedule) as schedule'),
                        )->get()->map(function ($d) use ($task, $date) {
                            return [
                                'Nombres_Apellidos' => $d->name,
                                'Num_SAP' => $d->id,
                                'schedule' => $d->schedule,
                                'times' => DetailScheduleTime::where([
                                    ['fecha', '=', $date],
                                    ['idTask', '=', $task['id']],
                                    ['idUsuario', '=', $d->id],
                                ])->get(),
                                'photo' =>  User::where('employeenumber',  str_pad(
                                    $d->id,
                                    8,
                                    '0',
                                    STR_PAD_LEFT
                                ))->first()->photo(),
                            ];
                        }),
                    ];
                }),
        );
    }

    public function moveEmployee(Request $request)
    {
        // $request->date = '2024-04-19';
        // $request->task = 4613;
        // $request->schedule = '1427';
        try {
            DB::beginTransaction();
            $conflict = [];
            $status = true;
            $schedule = Schedule::find($request->schedule);
            $mensaje = 'Se ha movido de tarea a: ' . $schedule->name . '.';
            $scheduleTimes = DetailScheduleTime::where('idUsuario', $schedule->employee_id)
                ->where('fecha', $request->date)->get();
            foreach ($scheduleTimes as $item) {
                $exist = DetailScheduleTime::where('fecha', $request->date)
                    ->where('idUsuario', $schedule->employee_id)
                    ->where(function ($query) use ($item) {
                        $query->where(function ($subquery) use ($item) {
                            $subquery->whereTime('horaInicio', '<=', $item->horaInicio)
                                ->whereTime('horaFin', '>=', $item->horaInicio);
                        })->orWhere(function ($subquery) use ($item) {
                            $subquery->whereTime('horaInicio', '<=', $item->horaFin)
                                ->whereTime('horaFin', '>=',  $item->horaFin);
                        })->orWhere(function ($subquery) use ($item) {
                            $subquery->whereBetween('horaInicio', [$item->horaInicio, $item->horaFin])
                                ->whereBetween('horaFin', [$item->horaInicio, $item->horaFin]);
                        });
                    })->get();

                if ($exist->count() > 0) {
                    if ($exist->count() == 1 && $exist[0]->fecha == Carbon::parse($schedule->fecha)->format('Y-m-d')) {
                        $schedule = Schedule::find($request->schedule);
                        Assignment::where('event', $schedule->task_id)
                            ->where('resource', $schedule->employee_id)->delete();
                        $schedule->task_id = $request->task;
                        $schedule->save();
                        //se agregan los recursos al cronograma.
                        $employee = searchEmpleados('Num_SAP', $schedule->employee_id)->first();

                        Assignment::firstOrCreate([
                            'event' => $request->task,
                            'resource' => $schedule->employee_id,
                            'units' => 100,
                            'name' => $schedule->name,
                            'costo_hora' => $employee->Costo_Hora
                        ]);
                    } else {
                        $exist = collect($exist)->each(function ($DetailScheduleTime) {
                            $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                        });
                        $conflict[$schedule->fecha] = $exist;
                        $mensaje = 'Se han generado colisiones al mover a: ' . $schedule->name . ' ';
                        $status = false;
                    }
                } else {

                    $newSchedule = Schedule::firstOrCreate([
                        'employee_id' => $schedule->employee_id,
                        'name' => $schedule->name,
                        'task_id' => $request->task,
                        'fecha' => $request->date
                    ]);
                    $newSchedule->save();
                    $scheduleTimes = ScheduleTime::where('schedule_id', $request->schedule)->get();
                    foreach ($scheduleTimes as $scheduleTime) {
                        ScheduleTime::firstOrCreate([
                            'schedule_id' => $newSchedule->id,
                            'hora_inicio' => $scheduleTime->hora_inicio,
                            'hora_fin' => $scheduleTime->hora_fin,
                        ]);
                    }
                    Assignment::where('resource', $schedule->employee_id)->where('event', '=', $schedule->task_id)->delete();
                    ScheduleTime::where('schedule_id', $schedule->id)->delete();
                    $schedule->delete();
                }
            }
            DB::commit();
            return response()->json([
                'status' => $status,
                'conflict' => $conflict,
                'mensaje' => $mensaje,
                'task' => $this->getSchedule($request->date, $request->task),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => $e->getMessage()]);
        }
    }

    public function copyPaste(Request $request)
    {
        try {
            $conflict = [];
            $status = true;
            DB::beginTransaction();
            $data = DetailScheduleTime::where('idTask', $request->task)
                ->where('fecha', Carbon::parse($request->date)->format('Y-m-d'))->get();
            if ($data->count() == 0) {
                return response()->json([
                    'status' => false,
                    'mensaje' => 'No hay personal para pegar en esta tarea'
                ]);
            }
            if (Carbon::parse($request->date)->eq(Carbon::parse($request->newDate)) && !$request->cut) {
                return response()->json([
                    'status' => false,
                    'mensaje' => 'No se puede copiar al personal para el mismo día'
                ]);
            }
            foreach ($data as $item) {
                $exist = DetailScheduleTime::where('fecha', $request->newDate)
                    ->where('idUsuario', $item->idUsuario)
                    ->where(function ($query) use ($item) {
                        $query->where(function ($subquery) use ($item) {
                            $subquery->whereTime('horaInicio', '<=', $item->horaInicio)
                                ->whereTime('horaFin', '>=', $item->horaInicio);
                        })->orWhere(function ($subquery) use ($item) {
                            $subquery->whereTime('horaInicio', '<=', $item->horaFin)
                                ->whereTime('horaFin', '>=',  $item->horaFin);
                        })->orWhere(function ($subquery) use ($item) {
                            $subquery->whereBetween('horaInicio', [$item->horaInicio, $item->horaFin])
                                ->whereBetween('horaFin', [$item->horaInicio, $item->horaFin]);
                        });
                    })->get();

                if ($exist->count() > 0) {
                    $exist = $exist->each(function ($DetailScheduleTime) {
                        $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                    });
                    $conflict[$request->date] = $exist;
                    $status = false;
                } else {
                    $employee = searchEmpleados('Num_SAP', $item->idUsuario)->first();
                    programming(
                        Carbon::parse($request->newDate)->format('Y-m-d'),
                        $item->idUsuario,
                        $item->horaInicio,
                        $item->horaFin,
                        $request->newTask,
                        $item->nombre,
                        $employee->Costo_Hora
                    );
                    if ($request->cut) {
                        disprogramming($item->idSchedule);
                    }
                }
            }
            DB::commit();
            return response()->json([
                'status' => $status,
                'mensaje' => 'Acción realizada',
                'conflict' => $conflict,
                'task' => $this->getSchedule($request->newDate, $request->newTask)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => $e->getMessage()]);
        }
    }

    public function removeAll(Request $request)
    {
        try {
            DB::beginTransaction();
            Schedule::whereIn('id', $request->schedules)->delete();
            ScheduleTime::whereIn('schedule_id', $request->schedules)->delete();
            DB::commit();
            return response()->json([
                'status' => true,
                'mensaje' => 'Personal eliminado'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => $e->getMessage()]);
        }
    }

    public function getProjectWithProgrammingDate(Request $request)
    {
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $projects = Schedule::where('fecha', $date)
            ->join('tasks as t', 't.id', '=', 'schedules.task_id')
            ->join('projects as p', 'p.id', '=', 't.project_id')
            ->select('p.id', DB::raw('MIN(p.name) as name'))
            ->groupBy('p.id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            });

        return $projects;
        return response()->json([
            'project' => $this->getProject($date),
            'programming' => $this->getProgramming($date),
        ]);
    }


    public function getDataContractor()
    {
        try {
            return response()->json([
                'status' => true,
                'data' => ContractorEmployee::select('id', 'contractor')->distinct()->get()
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'mensaje' => $e->getMessage()]);
        }
    }
}
