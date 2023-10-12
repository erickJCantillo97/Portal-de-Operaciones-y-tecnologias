<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Gantt\Task;
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

        $task = Schedule::where('fecha', $validateData['fecha'])->with('scheduleTimes')->where('task_id', $validateData['task_id'])->get();

            return response()->json([
                'status' => true,
                'task' => $task,
            ], 200);
        } catch (Exception $e) {
            return $e;
        }

    }

    public function indexGEMAM()
    {
        return Inertia::render('Programming/IndexGEMAM');
    }

    public function delete(Request $request)
    {
        //hace algo
        return 'Hecho';
    }

    public function endNivelActivities(Request $request)
    {
        if (isset($request->dates[0])) {
            $date_start = Carbon::parse($request->dates[0])->format('Y-m-d');
            $date_end = Carbon::parse($request->dates[1])->format('Y-m-d');
        } else {
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

    public function getScheduleTask( Request $request){
        $date = Carbon::parse($request->date)->format('Y-m-d');

        $schedule = Schedule::where('fecha', $date)->with('scheduleTimes')->where('task_id', $request->task_id)->get();

        return response()->json([
            'schedule' => $schedule,
        ], 200);
    }

}
