<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Views\DetailScheduleTime;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\Shift;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use DateTime;


class ProgrammingController extends Controller
{
    public function index()
    {
        return Inertia::render('Programming/Index');
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
            if ($hours < 9.5) {
                $task = VirtualTask::find($validateData['task_id']);
                $date = Carbon::parse($validateData['fecha']);
                $end_date = Carbon::parse($task->endDate);
                do {
                    $exist = DetailScheduleTime::where('idUsuario', $validateData['employee_id'])
                        ->where('fecha', $date)
                        ->get();
                    if ($exist->count() > 0) {
                        $exist = $exist->each(function ($DetailScheduleTime) {
                            $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                        });
                        $conflict[$date->format('Y-m-d')] = $exist;
                        $date = $date->addDays(1);
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
                        $date = $date->addDays(1);
                    }
                } while ($end_date->gte($date));
                $status = true;
                $codigo = 0;
                $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);
            }
            DB::commit();
            return response()->json([
                'status' => $status,
                'codigo' => $codigo,
                'task' => $this->getSchedule($validateData['fecha'], $validateData['task_id']),
                'hours' => $hours,
                'conflict' => $conflict
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
            })->whereNotIn('id', array_unique($taskWithSubTasks))->take(2)->get()->map(function ($task) {
                return [
                    'name' => $task['name'],
                    'id' => $task['id'],
                    'task' => $task->task->name,
                    'endDate' => $task['endDate'],
                    'percentDone' => $task['percentDone'],
                    'project' => $task->project->name,
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
            $mensaje = '';
            $error = false;
            $conflict = [];
            $validateData = $request->validate([
                'name' => 'required',
                'startShift' => 'date_format:"H:i"|required',
                'endShift' => 'date_format:"H:i"|required',
                'type' => 'numeric|required',
                'date' => 'date|required',
                'personalized' => 'boolean|required',
                'idUser' => 'required',
                'schedule' => 'required',
                'details' => 'required'
            ]);
            if ($request->personalized) {
                Shift::firstOrCreate([
                    'name' => $validateData['name'],
                    'startShift' => new DateTime("1970-01-01T" . $validateData['startShift'] . ":00"),
                    'endShift' => new DateTime("1970-01-01T" . $validateData['endShift'] . ":00"),
                    'status' => false,
                    'customized' => $validateData['idUser']
                ]);
            }
            switch ($request->type) {
                case 1:
                    $exist = DetailScheduleTime::where('idUsuario', $validateData['idUser'])
                        ->where('fecha', $request->date)
                        ->whereBetween('horaInicio', [$validateData['startShift'], $validateData['endShift']])
                        ->whereBetween('horaFin', [$validateData['startShift'], $validateData['endShift']])
                        ->get();
                    if ($exist->count() > 0) {
                        $mensaje = "No se pudo programar al usuario: " . $exist->nombre . " porque para la fecha:" . $request->date . " está asignado a otra actividad: ";
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'mensaje' => $mensaje,
                            'response_type' => "question", //question y success
                            'conflict' => $exist
                        ]);
                    }
                    ScheduleTime::create([
                        'schedule_id' => $validateData['schedule'],
                        'hora_inicio' => $validateData['startShift'],
                        'hora_fin' => $validateData['endShift']
                    ]);
                    $mensaje = "Horario asignado correctamente";
                    break;
                case 2:
                    $task = VirtualTask::find(Schedule::find($validateData['schedule'])->task_id);
                    $date = Carbon::parse($validateData['date']);
                    $end_date = Carbon::parse($task->endDate);
                    do {
                        $exist = DetailScheduleTime::where('idUsuario', $validateData['idUser'])
                            ->whereBetween('horaInicio', [$validateData['startShift'], $validateData['endShift']])
                            ->whereBetween('horaFin', [$validateData['startShift'], $validateData['endShift']])
                            ->where('fecha', $date)
                            ->get();
                        if ($exist->count() > 0) {
                            $exist = $exist->each(function ($DetailScheduleTime) {
                                $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                            });
                            $conflict[$date->format('Y-m-d')] = $exist;
                            $date = $date->addDays(1);
                            $error = true;
                            $mensaje = "No se pudo programar al usuario: " . $exist->nombre . " porque está asignado a otra actividad: ";
                        } else {
                            $schedule = Schedule::firstOrNew([
                                'task_id' => $task->task_id,
                                'employee_id' => $validateData['idUser'],
                                'name' => $validateData['name'],
                                'fecha' => $date->format('Y-m-d'),
                            ]);
                            $schedule->save();
                            ScheduleTime::create([
                                'schedule_id' => $schedule->id,
                                'hora_inicio' => Carbon::parse($validateData['startShift'])->format('H:i'),
                                'hora_fin' => Carbon::parse($validateData['endShift'])->format('H:i'),
                            ]);
                            $date = $date->addDays(1);
                        }
                    } while ($end_date->gte($date));
                    if (!$error) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'mensaje' => $mensaje,
                            'response_type' => "question", //question y success
                            'conflict' => $conflict
                        ]);
                    }
                    $mensaje = "Horario asignado correctamente";
                    break;
                case 3:
                    $task = VirtualTask::find(Schedule::find($validateData['schedule'])->task_id);
                    $date = Carbon::parse($request->details[0]);
                    $end_date = Carbon::parse($request->details[1]);
                    do {
                        $exist = DetailScheduleTime::where('idUsuario', $validateData['idUser'])
                            ->whereBetween('horaInicio', [$validateData['startShift'], $validateData['endShift']])
                            ->whereBetween('horaFin', [$validateData['startShift'], $validateData['endShift']])
                            ->where('fecha', $date)
                            ->get();
                        if ($exist->count() > 0) {
                            $exist = $exist->each(function ($DetailScheduleTime) {
                                $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                            });
                            $conflict[$date->format('Y-m-d')] = $exist;
                            $date = $date->addDays(1);
                            $error = true;
                            $mensaje = "No se pudo programar al usuario: " . $exist->nombre . " porque está asignado a otra actividad: ";
                        } else {
                            $schedule = Schedule::firstOrNew([
                                'task_id' => $task->task_id,
                                'employee_id' => $validateData['idUser'],
                                'name' => $validateData['name'],
                                'fecha' => $date->format('Y-m-d'),
                            ]);
                            $schedule->save();
                            ScheduleTime::create([
                                'schedule_id' => $schedule->id,
                                'hora_inicio' => Carbon::parse($validateData['startShift'])->format('H:i'),
                                'hora_fin' => Carbon::parse($validateData['endShift'])->format('H:i'),
                            ]);
                            $date = $date->addDays(1);
                        }
                    } while ($end_date->gte($date));
                    if (!$error) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'mensaje' => $mensaje,
                            'response_type' => "question", //question y success
                            'conflict' => $conflict
                        ]);
                    }
                    $mensaje = "Horario asignado correctamente";
                    break;
                case 4:
                    $task = VirtualTask::find(Schedule::find($validateData['schedule'])->task_id);
                    foreach ($request->details as $date) {
                        $exist = DetailScheduleTime::where('idUsuario', $validateData['idUser'])
                            ->whereBetween('horaInicio', [$validateData['startShift'], $validateData['endShift']])
                            ->whereBetween('horaFin', [$validateData['startShift'], $validateData['endShift']])
                            ->where('fecha', $date)
                            ->get();
                        if ($exist->count() > 0) {
                            $exist = $exist->each(function ($DetailScheduleTime) {
                                $DetailScheduleTime->taskDetails = VirtualTask::find($DetailScheduleTime->idTask);
                            });
                            $conflict[$date] = $exist;
                            $error = true;
                            $mensaje = "No se pudo programar al usuario: " . $exist->nombre . " porque está asignado a otra actividad: ";
                        } else {
                            $schedule = Schedule::firstOrNew([
                                'task_id' => $task->task_id,
                                'employee_id' => $validateData['idUser'],
                                'name' => $validateData['name'],
                                'fecha' => $date,
                            ]);
                            $schedule->save();
                            ScheduleTime::create([
                                'schedule_id' => $schedule->id,
                                'hora_inicio' => Carbon::parse($validateData['startShift'])->format('H:i'),
                                'hora_fin' => Carbon::parse($validateData['endShift'])->format('H:i'),
                            ]);
                            $date = $date->addDays(1);
                        }
                    }
                    if (!$error) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'mensaje' => $mensaje,
                            'response_type' => "question", //question y success
                            'conflict' => $conflict
                        ]);
                    }
                    $mensaje = "Horario asignado correctamente";
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'mensaje' => "Opcion no valida"
                    ]);
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'mensaje' => $mensaje,
                'response_type' => "success",
                'conflict' => []
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $e;
        }
    }
}
