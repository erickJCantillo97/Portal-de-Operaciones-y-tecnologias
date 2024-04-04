<?php

namespace App\Http\Controllers\Personal;

use App\Models\Personal\ExtendedSchedule;
use App\Models\VirtualTask;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Carbon;


class ExtendedScheduleController extends Controller
{

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            foreach($request->dates as $date){
                foreach($request->task_id as $taks){
                    ExtendedSchedule::create([
                        'date' => $date,
                        'start_hour' => $request->start_hour,
                        'end_hour' => $request->end_hour,
                        'project_id' => $request->project_id,
                        'task_id' => $taks,
                        'description'=>  $request->description
                    ]);
                }
            }
            DB::commit();
            return response()->json(['status'=>true, 'message'=>'Registro guardado']);

        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '. $e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try{
            $extendedSchedule = ExtendedSchedule::find($request->id);
            $extendedSchedule->date = $request->date;
            $extendedSchedule->start_hour = $request->start_hour;
            $extendedSchedule->end_hour = $request->end_hour;
            $extendedSchedule->save();
            return response()->json(['status'=>true, 'mensaje'=>'Registro actualizado']);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $extendedSchedule = ExtendedSchedule::find($id);
            $extendedSchedule->delete();
            return response()->json(['status'=>true, 'mensaje'=>'Registro eliminado']);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }

    public function all(){
        return response()->json([
            'data'=>ExtendedSchedule::where('date','>=', Carbon::now()->format('Y-m-d'))
        ]);
    }

    public function getTask ($project)
    {
        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->distinct()->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();
        return response()->json(
            VirtualTask::where('percentDone', '<',100)->where('project_id', $project)
              ->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task){
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
