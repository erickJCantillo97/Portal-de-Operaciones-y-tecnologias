<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Personal\ExtendedSchedule;
use App\Models\VirtualTask;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExtendedScheduleController extends Controller
{
    public function index()
    {
        $extendsEschedule = ExtendedSchedule::where('date', '>=', Carbon::now()->format('Y-m-d'))->get();

        return response()->json([
            'data' => [],
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->dates as $d) {
                foreach ($request->tasks as $t) {
                    // dd($t);
                    ExtendedSchedule::create([
                        'date' => $d,
                        'start_hour' => $request->start_hour,
                        'end_hour' => $request->end_hour,
                        'project_id' => VirtualTask::findOrFail($t)->project_id,
                        'task_id' => $t,
                        'description' => $request->description,
                    ]);
                }
            }
            DB::commit();

            // return response()->json(['status' => true, 'message' => 'Registro guardado']);
        } catch (Exception $e) {
            DB::rollBack();
            // dd($e);

            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExtendedSchedule $extendedSchedule)
    {
        // return $request->dates;
        try {
           

            dd($extendedSchedule);
            $extendedSchedule->date = Carbon::parse($request->dates)->format('Y-m-d');
            $extendedSchedule->start_hour = $request->start_hour;
            $extendedSchedule->end_hour = $request->end_hour;
            $extendedSchedule->description = $request->description;
            $extendedSchedule->save();

            return response()->json(['status' => true, 'mensaje' => 'Registro actualizado']);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExtendedSchedule $extendedSchedule)
    {
        try {
            $extendedSchedule->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }

    public function getTask($project)
    {
        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->distinct()->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();

        return response()->json(
            VirtualTask::where('percentDone', '<', 100)->where('project_id', $project)
                ->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task) {
                    return [
                        'name' => $task['name'],
                        'id' => $task['id'],
                        'task' => $task->task->name,
                        'taskdad' => $task->task->task->name ?? '',
                        'endDate' => $task['endDate'],
                        'startDate' => $task['startDate'],
                        'percentDone' => $task['percentDone'],
                        'project_id' => $task['project_id'],
                    ];
                }),
        );
    }
}
