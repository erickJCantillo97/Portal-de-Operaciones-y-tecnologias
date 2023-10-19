<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

            $status = true;
            $codigo = 0;
            $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);

            if($hours < 9.5){
                $schedule = Schedule::firstOrNew($validateData);
                $schedule->save();

                ScheduleTime::create([
                    'schedule_id' => $schedule->id,
                    'hora_inicio' => '7:00',
                    'hora_fin' => '12:00'
                ]);
            }else{
                $status = false;
                $codigo = 1001; //Codigo para el caso en se trate de programar alguien con mas de 9.5 horas
            }

            $hours = $this->getAssignmentHour($validateData['fecha'], $validateData['employee_id']);

            return response()->json([
                'status' => $status,
                'codigo' => $codigo,
                'task' => $this->getSchedule($validateData['fecha'],$validateData['task_id'] ),
                'hours' => $hours
            ], 200);
        } catch (Exception $e) {
            return $e;
        }

    }

    public function indexGEMAM()
    {
        return Inertia::render('Programming/IndexGEMAM');
    }

    public function deleteSchedule(Schedule $schedule)
    {
        $schedule->delete();
        ScheduleTime::where('schedule_id', $schedule->id)->delete();
        return response()->json([
            'status' => true,
            'task' => $this->getSchedule($schedule->fecha, $schedule->task_id ),
            'hours' => $this->getAssignmentHour($schedule->fecha, $schedule->employee_id)
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
        }
        elseif(isset($request->date)){
            $date_start = Carbon::parse($request->date)->format('Y-m-d');
            $date_end = Carbon::parse($request->date)->format('Y-m-d');
        }
        else {
            $date_start = Carbon::now()->format('Y-m-d');
            $date_end = Carbon::now()->format('Y-m-d');
        }

        $tareas = VirtualTask::whereNotNull('task_id')->select('task_id')->get()->toArray();

        $ids = array_map(function ($objeto) {
            return $objeto['task_id'];
        }, $tareas);

        return response()->json(
            VirtualTask::where(function ($query) use ($date_start, $date_end) {
                $query->whereBetween('startdate', [$date_start, $date_end])
                    ->orWhereBetween('enddate', [$date_start, $date_end])
                    ->orWhere(function ($query) use ($date_start, $date_end) {
                        $query->where('enddate', '>', $date_end)
                            ->where('startdate', '<', $date_start);
                    });
            })->whereNotIn('id', array_unique($ids))->get()->map(function ($task) {
                return [
                    'name' => $task['name'],
                    'id' => $task['id'],
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
    private function getSchedule($fecha, $taskId){
        return  Schedule::where('fecha', $fecha)->with('scheduleTimes')->where('task_id', $taskId)->get();
    }


    /*
    * Esta función obtiene el numero de horas asignadas a una persona en una fecha especifica
    */
    public function getAssignmentHour($fecha, $userId){
        $schedule = Schedule::where([
            'fecha' => $fecha,
            'employee_id' => $userId
        ])->pluck('id')->toArray();

        $horas_acumulados = ScheduleTime::whereIn('schedule_id' , $schedule,)->selectRaw('SUM(datediff(mi,hora_inicio, hora_fin)) as diferencia_acumulada')->get();

        return  $horas_acumulados[0]->diferencia_acumulada/60;
    }


    public function getTimesSchedulesEmployee(Request $request){
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $schedulesIds = Schedule::where('fecha', $date)->with('scheduleTimes')->where('employee_id', $request->employee_id)->pluck('id')->toArray();

        $times = ScheduleTime::whereIn('schedule_id', $schedulesIds)->with('schedule', 'schedule.task', 'schedule.task.project')->get()->map(function ($time) use($date){
            return [
                'id' => $time['id'],
                'hora_inicio' => Carbon::parse($date . $time['hora_inicio'])->toIso8601String(),
                'hora_fin' => Carbon::parse($date . $time['hora_fin'])->toIso8601String(),
                'task' => $time['schedule']['task']['name'],
                'project' => $time['schedule']['task']['project']['name'],
            ];
        });

        return response()->json([
            'times' => $times,
        ]);

    }
}
