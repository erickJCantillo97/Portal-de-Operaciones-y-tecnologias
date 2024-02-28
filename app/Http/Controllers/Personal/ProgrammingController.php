<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\Shift;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgrammingController extends Controller
{
    public function index()
    {
        return Inertia::render('Programming/Index', [
            'hours' => Shift::get()
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
            $validateData = $request->validate([
                'task_id' => 'required',
                'employee_id' => 'required',
                'name' => 'required',
                'fecha' => 'required|date',
            ]);

            $status = false;
            $codigo = 1001; //Codigo para el caso en se trate de programar alguien con mas de 9.5 horas
            $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);
            if ($hours < 9.5) {
                $schedule = Schedule::firstOrNew($validateData);
                $schedule->save();
                ScheduleTime::create([
                    'schedule_id' => $schedule->id,
                    'hora_inicio' => '7:00',
                    'hora_fin' => '16:30',
                ]);
                $status = true;
                $codigo = 0;
                $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);
            }


            return response()->json([
                'status' => $status,
                'codigo' => $codigo,
                'task' => $this->getSchedule($validateData['fecha'], $validateData['task_id']),
                'hours' => $hours,
            ], 200);
        } catch (Exception $e) {
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
        $year = explode('-', $request->week)[0];
        $week_number = str_replace('W', '', explode('-', $request->week)[1]);
        $fecha = Carbon::createFromDate($year, 1, 1);

        // Agregar el número de semanas menos uno (ya que queremos la semana anterior)
        $fecha->addWeeks($week_number - 1);

        // Obtener el lunes de esa semana
        $lunes = $fecha->startOfWeek();

        // Obtener el viernes de esa semana
        $viernes = $fecha->copy()->endOfWeek()->subDays(2);


        // if (isset($request->dates[0])) {
        //     $lunes = Carbon::parse($request->dates[0])->format('Y-m-d');
        //     $date_end = Carbon::parse($request->dates[1])->format('Y-m-d');
        // } elseif (isset($request->date)) {
        //     $date_start = Carbon::parse($request->date)->format('Y-m-d');
        //     $date_end = Carbon::parse($request->date)->format('Y-m-d');
        // } else {
        //     $date_start = Carbon::now()->format('Y-m-d');
        //     $date_end = Carbon::now()->format('Y-m-d');
        // }

        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();



        return response()->json(
            VirtualTask::has('project')->where(function ($query) use ($lunes, $viernes) {
                $query->whereBetween('startdate', [$lunes, $viernes])
                    ->orWhereBetween('enddate', [$lunes, $viernes])
                    ->orWhere(function ($query) use ($lunes, $viernes) {
                        $query->where('enddate', '>', $viernes)
                            ->where('startdate', '<', $lunes);
                    });
            })->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task) {
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

        $schedule = Schedule::where('fecha', $date)->with('scheduleTimes')->where('task_id', $request->task_id)->get();

        return response()->json([
            'schedule' => $schedule,
        ], 200);
    }

    private function getSchedule($fecha, $taskId)
    {
        return Schedule::where('fecha', $fecha)->with('scheduleTimes')->where('task_id', $taskId)->get();
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
}
