<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Jobs\Personal\PersonalScheduleDayJob;
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

        return Inertia::render('Programming/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $projects = Project::active()->get();

        return Inertia::render('Programming/Create', [
            'projects' => $projects,
        ]);
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
        try {
            DB::beginTransaction();
            $validateData = $request->validate([
                'task_id' => 'required',
                'employee_id' => 'required',
                'name' => 'required',
                'fecha' => 'required|date', //fecha seleccionada del calendario
            ]);

            $status = false;
            $codigo = 1001; //Codigo para el caso en se trate de programar alguien con mas de 9.5 horas
            $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);
            $conflict = [];
            $end_date = '';
            $project = Project::find(VirtualTask::find($request->task_id)->project_id);
            if ($hours < 9.5) {
                $task = VirtualTask::find($validateData['task_id']);
                $date = Carbon::parse($validateData['fecha']);
                if ($project->daysPerWeek == 5) {
                    $end_date = Carbon::parse($validateData['fecha'])->next(Carbon::FRIDAY);
                } else if ($project->daysPerWeek == 6) {
                    $end_date = Carbon::parse($validateData['fecha'])->next(Carbon::SATURDAY);
                } else {
                    $end_date = Carbon::parse($validateData['fecha'])->next(Carbon::SUNDAY);
                }
                // if($end_date < Carbon::parse($validateData['fecha'])){
                //     return $end_date." es menor";
                // }
                // return $end_date." es mayor";
                do {
                    if (getWorkingDays($date->format('Y-m-d'), intval($project->daysPerWeek))) {
                        $exist = DetailScheduleTime::where('idUsuario', $validateData['employee_id'])
                            ->where('fecha', $date)
                            ->get();
                        if ($exist->count() > 0) {
                            //obtengo las actividades diferentes a la actual
                            $exist = $exist->filter(function ($item) use ($validateData) {
                                return $item->idTask != $validateData['task_id'];
                            })->values()->all();

                            // se agrega el task a las actividades que generan conflictos.
                            $exist = collect($exist)->each(function ($DetailScheduleTime) {
                                $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                            });
                            // se guardan las actividades que generan conflictos
                            if ($exist->count() > 0) {
                                $conflict[$date->format('Y-m-d')] = $exist;
                                $status = false;
                            }
                        } else {
                            $schedule = Schedule::firstOrNew([
                                'task_id' => $validateData['task_id'],
                                'employee_id' => $validateData['employee_id'],
                                'name' => $validateData['name'],
                                'fecha' => $date->format('Y-m-d'),
                            ]);
                            $schedule->save();
                            ScheduleTime::create([
                                'schedule_id' => $schedule->id,
                                'hora_inicio' => Carbon::parse($task->project->shiftObject->startShift)->format('H:i'),
                                'hora_fin' => Carbon::parse($task->project->shiftObject->endShift)->format('H:i'),
                            ]);
                        }
                    }
                    $date = $date->addDays(1);
                } while ($end_date->gte($date));
                $codigo = 0;
                $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);
            }
            DB::commit();

            return response()->json([
                'status' => $status,
                'codigo' => $codigo,
                'task' => $this->getSchedule($validateData['fecha'], $validateData['task_id']),
                'hours' => $hours,
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

    private function getSchedule($fecha, $taskId)
    {
        return Schedule::where('fecha', $fecha)->with('scheduleTimes')->where('task_id', $taskId)->get()->sortBy([
            ['is_my_personal', 'desc'],
        ]);
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

        $horas_acumulados = ScheduleTime::whereIn('schedule_id', $schedule)->selectRaw('SUM(datediff(mi,hora_inicio, hora_fin)) as diferencia_acumulada')->get();

        // dd($horas_acumulados);
        return $horas_acumulados[0]->diferencia_acumulada / 60;
    }

    public function getTimesSchedulesEmployee(Request $request)
    {
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $schedulesIds = Schedule::where('fecha', $date)->with('scheduleTimes')->where('employee_id', $request->employee_id)->pluck('id')->toArray();
        $times = ScheduleTime::whereIn('schedule_id', $schedulesIds)->with('schedule', 'schedule.task', 'schedule.task.project')->get()->map(function ($time) use ($date) {
            return [
                'id' => $time['id'],
                'start' => $date . 'T' . Carbon::parse($time['hora_inicio'])->format('H:i:s'),
                'end' => $date . 'T' . Carbon::parse($time['hora_fin'])->format('H:i:s'),
                'title' => $time['schedule']['task']['name'] . ' - (' . $time['schedule']['task']['project']['name'] . ')',
                'project' => $time['schedule']['task']['project']['name'],
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
            $task = VirtualTask::find(Schedule::find($request->schedule)->task_id);
            $project = Project::find($task->project_id);
            if ($request->personalized) {
                Shift::firstOrCreate([
                    'name' => $request->timeName,
                    'startShift' => new DateTime('1970-01-01T' . $request->startShift . ':00'),
                    'endShift' => new DateTime('1970-01-01T' . $request->endShift . ':00'),
                    'timeBreak' => $request->timeBreak,
                    'status' => false,
                    'user' => $request->idUser,
                ]);
            }
            switch ($request->type) {
                    //SOLO EL $request->date
                case 1:
                    $exist = DetailScheduleTime::where('fecha', $request->date)
                        ->where('idSchedule', '!=', $request->schedule)
                        ->where('idUsuario', $request->idUser)
                        ->where(function ($query) use ($request) {
                            $query->where(function ($subquery) use ($request) {
                                $subquery->whereTime('horaInicio', '<=', $request->startShift)
                                    ->whereTime('horaFin', '>=', $request->startShift);
                            })->orWhere(function ($subquery) use ($request) {
                                $subquery->whereTime('horaInicio', '<=', $request->endShift)
                                    ->whereTime('horaFin', '>=',  $request->endShift);
                            })->orWhere(function ($subquery) use ($request) {
                                $subquery->whereBetween('horaInicio', [$request->startShift, $request->endShift])
                                    ->whereBetween('horaFin', [$request->startShift, $request->endShift]);
                            });
                        })->get();
                    // return $exist;
                    if ($exist->count() > 0) {
                        $exist = $exist->each(function ($DetailScheduleTime) {
                            $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                        });
                        $conflict[$request->date] = $exist;
                    } else {
                        if (getWorkingDays($request->date, $project->daysPerWeek)) {
                            $ScheduleTime = ScheduleTime::find($request->schedule_time);
                            $ScheduleTime->hora_inicio = $request->startShift;
                            $ScheduleTime->hora_fin = $request->endShift;
                            $ScheduleTime->save();
                        } else {
                            $conflict[$request->date] = $exist;
                        }
                    }
                    break;
                case 2:
                    //RESTO DE LA ACTIVIDAD
                    $date = Carbon::parse($request->date);
                    $end_date = Carbon::parse($task->endDate);
                    //esta consulta se usa para saber si la persona está programada en una actividad
                    //diferente a la actual (La primera)
                    $firts = DetailScheduleTime::where('fecha', $request->date)
                        ->where('idSchedule', '!=', $request->schedule)
                        ->where('idUsuario', $request->idUser)
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

                    if ($firts->count() > 0) {
                        $firts = $firts->each(function ($DetailScheduleTime) {
                            $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                        });
                        $conflict[$date->format('Y-m-d')] = $firts;
                    } else {
                        $ScheduleTime = ScheduleTime::find($request->schedule_time);
                        $ScheduleTime->hora_inicio = $request->startShift;
                        $ScheduleTime->hora_fin = $request->endShift;
                        $ScheduleTime->save();
                    }
                    $date = $date->addDays(1);
                    do {
                        $exist = DetailScheduleTime::where('fecha', $date)
                            ->where('idUsuario', $request->idUser)
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
                            $conflict[$date->format('Y-m-d')] = $exist;
                        } else {
                            if (getWorkingDays($date, $project->daysPerWeek)) {
                                $schedule = Schedule::firstOrNew([
                                    'task_id' => $task->id,
                                    'employee_id' => $request->idUser,
                                    'name' => $request->userName,
                                    'fecha' => $date->format('Y-m-d'),
                                ]);
                                $schedule->save();
                                $ScheduleTime = ScheduleTime::create([
                                    'schedule_id' => $schedule->id,
                                    'hora_inicio' => Carbon::parse($request->startShift)->format('H:i'),
                                    'hora_fin' => Carbon::parse($request->endShift)->format('H:i'),
                                ]);
                                $ScheduleTime->save();
                            }
                        }
                        $date = $date->addDays(1);
                    } while ($end_date->gte($date));
                    // return $date;
                    break;
                case 3:
                    //  RANGO DE FECHAS
                    $date = Carbon::parse($request->details[0]);
                    $end_date = Carbon::parse($request->details[1]);

                    //esta validacion verifica que el rango de fechas seleccionadas esté dentro del rango de fecha de la actividad
                    if (Carbon::parse($task->startDate)->gt($end_date) || Carbon::parse(($task->endDate))->lt($date)) {
                        return response()->json([
                            'status' => false,
                            'mensaje' => 'No se puede programar en el rango de fecha seleccionado porque no concuerdan con el rango de fecha de la actividad.',
                            'conflict' => $conflict,
                        ]);
                    }
                    do {
                        $exist = DetailScheduleTime::where('fecha', $date)
                            ->where('idUsuario', $request->idUser)
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
                            $conflict[$date->format('Y-m-d')] = $exist;
                        } else {
                            if (getWorkingDays($request->date, $project->daysPerWeek)) {
                                $schedule = Schedule::firstOrNew([
                                    'task_id' => $task->id,
                                    'employee_id' => $request->idUser,
                                    'name' => $request->userName,
                                    'fecha' => $date->format('Y-m-d'),
                                ]);
                                $schedule->save();
                                $ScheduleTime = ScheduleTime::create([
                                    'schedule_id' => $schedule->id,
                                    'hora_inicio' => Carbon::parse($request->startShift)->format('H:i'),
                                    'hora_fin' => Carbon::parse($request->endShift)->format('H:i'),
                                ]);
                                $ScheduleTime->save();
                            }
                        }
                        $date = $date->addDays(1);
                    } while ($end_date->gte($date));
                    break;
                case 4:
                    // FECHAS ESPECIFICAS
                    foreach ($request->details as $date) {

                        $exist = DetailScheduleTime::where('fecha', $date)
                            ->where('idUsuario', $request->idUser)
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
                            $conflict[$date] = $exist;
                        } else {
                            if (getWorkingDays($date, $project->daysPerWeek)) {
                                $schedule = Schedule::firstOrNew([
                                    'task_id' => $task->id,
                                    'employee_id' => $request->idUser,
                                    'name' => $request->userName,
                                    'fecha' => $date,
                                    'status' => 0,
                                ]);
                                $schedule->save();
                                $ScheduleTime = ScheduleTime::create([
                                    'schedule_id' => $schedule->id,
                                    'hora_inicio' => Carbon::parse($request->startShift)->format('H:i'),
                                    'hora_fin' => Carbon::parse($request->endShift)->format('H:i'),
                                ]);
                                $ScheduleTime->save();
                            }
                        }
                    }
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'mensaje' => 'Opcion no valida',
                        'response_type' => 'Error', //question y success
                        'conflict' => [],
                    ]);
            }
            DB::commit();
            if (count($conflict) > 0) {
                return response()->json(['status' => false, 'conflict' => $conflict, 'mensaje' => '', 'task' => $this->getSchedule($request->date, $task->id)]);
            }

            return response()->json(['status' => true, 'conflict' => [], 'mensaje' => 'Horario asignado', 'task' => $this->getSchedule($request->date, $task->id)]);
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
            $DetailScheduleTime = DetailScheduleTime::where('idScheduleTime', $request->scheduleTime)->first();
            $mensaje = '';
            switch ($request->actionType) {
                case 1:
                    $schedule = Schedule::find($DetailScheduleTime->idSchedule);
                    $schedule->task_id = $request->idTask;
                    $schedule->save();
                    if ($request->endSchedule) {
                        PersonalScheduleDayJob::dispatch($DetailScheduleTime->fecha, $DetailScheduleTime->idUsuario, $request->idTask)->onConnection('sync');
                        $mensaje = 'registro actualizado';
                    }
                    break;
                case 2:
                    if ($request->endSchedule) {
                        PersonalScheduleDayJob::dispatch($DetailScheduleTime->fecha, $DetailScheduleTime->idUsuario, $request->idTask)->onConnection('sync');
                        $mensaje = 'registro actualizado';
                    }
                    break;
                default:
                    break;
            }
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => $mensaje]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => true, 'mensaje' => 'Se ha generado un error: ' . $e->getMessage()]);
        }
    }

    public function removeSchedule(Request $request)
    {
        try {
            DB::beginTransaction();
            switch ($request->type) {
                    //SOLO EL $request->date
                case 1:
                    $schedule = Schedule::find($request->schedule);
                    $schedule->delete();
                    ScheduleTime::where('schedule_id', $schedule->id)->delete();
                    break;
                case 2:
                    //RESTO DE LA ACTIVIDAD
                    $schedule = Schedule::where('fecha', '>=', $request->date)
                        ->where('employee_id', $request->idUser);
                    $itemsToRemove = $schedule->get()->map(function ($item) {
                        return $item->id;
                    });
                    ScheduleTime::whereIn('schedule_id', $itemsToRemove)->delete();
                    $schedule->delete();
                    break;
                case 3:
                    //RANGO DE FECHAS
                    $schedule = Schedule::where('employee_id', $request->idUser)
                        ->whereBetween('fecha', [$request->details[0], $request->details[1]])->get();
                    $itemsToRemove = $schedule->map(function ($item) {
                        return $item->id;
                    });
                    ScheduleTime::whereIn('schedule_id', $itemsToRemove)->delete();
                    Schedule::whereBetween('fecha', [$request->details[0], $request->details[1]])->delete();
                    break;
                case 4:
                    //FECHAS ESPECIFICAS
                    foreach ($request->details as $date) {
                        $schedule = Schedule::where('fecha', '=', $date)
                            ->where('employee_id', '=', $request->idUser)->get()->first();
                        ScheduleTime::where('schedule_id', $schedule->id)->delete();
                        Schedule::where('fecha', $date)
                            ->where('employee_id', $request->idUser)->delete();
                    }
                    break;
                default:
                    break;
            }
            DB::commit();

            return response()->json(['status' => true, 'mensaje' => 'Horario eliminado']);
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
        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();

        return response()->json(
            VirtualTask::has('project')->has('task')
                ->where('project_id', $project->id)
                // ->where('executor','LIKE ,'%'.$request->executor.'%)
                ->where('percentDone', '<', 100)
                ->where('startDate', '<=', $request->date)
                // ->where(function ($query) use ($request) {
                //     $query->whereBetween('startdate', [$request->date_start, $request->date_end])
                //         ->orWhereBetween('enddate', [$request->date_start, $request->date_end])
                //         ->orWhere(function ($query) use ($request) {
                //             $query->where('enddate', '>', $request->date_end)
                //                 ->where('startdate', '<', $request->date_start);
                //         });
                ->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task) use ($request) {
                    return [
                        'name' => $task['name'],
                        'id' => $task['id'],
                        'task' => $task->task->name,
                        'taskdad' => $task->task->task->name ?? '',
                        'endDate' => $task['endDate'],
                        'startDate' => $task['startDate'],
                        'percentDone' => $task['percentDone'],
                        'shift' => $task->project->shift ? Shift::where('id', $task->project->shift)->first() : null,
                        'employees' => Schedule::where('task_id', $task['id'])->where('fecha', $request->date)->get(),
                    ];
                }),
        );
    }
}
