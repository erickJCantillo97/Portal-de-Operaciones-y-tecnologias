<?php

namespace App\Http\Controllers\Projects;

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
     * información como el método de solicitud, encabezados y datos de entrada.
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

            $schedule = Schedule::firstOrNew($validateData);
            $schedule->save();

            ScheduleTime::create([
                'schedule_id' => $schedule->id,
                'hora_inicio' => '7:00',
                'hora_fin' => '16:30'
            ]);

            return response()->json([
                'status' => true,
                'task' => $this->getSchedule($validateData['fecha'],$validateData['task_id'] ),
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
}
