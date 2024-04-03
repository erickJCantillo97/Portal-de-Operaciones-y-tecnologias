<?php

namespace App\Http\Controllers\Personal;

use App\Models\Personal\ExtendedSchedule;
use App\Models\VirtualTask;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ExtendedScheduleController extends Controller
{

    public function store(Request $request)
    {
        return "Acción: store, Hace algo (Desde el controlador) ";
        $validateData = $request->validate([
            //
        ]);

        try{
            ExtendedSchedule::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExtendedSchedule $extendedSchedule)
    {
        return "Acción: update, Hace algo (Desde el controlador) ";
        $validateData = $request->validate([
            //
        ]);

        try{
            $extendedSchedule->update($validateData);
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
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }

    public function all(){
        return response()->json(['data'=>ExtendedSchedule::all()]);
    }

    public function getTask ($project)
    {
        $taskWithSubTasks = VirtualTask::has('project')->whereNotNull('task_id')->select('task_id')->get()->map(function ($task) {
            return $task['task_id'];
        })->toArray();

        return response()->json(
            VirtualTask::has('project')->whereHas('task', function ($query){
                $query->where('percentDone', '<', 100);
            })->where('project_id', $project)
              ->whereNotIn('id', array_unique($taskWithSubTasks))->get()->map(function ($task){
                    return [
                        'name' => $task['name'],
                        'id' => $task['id'],
                        'task' => $task->task->name,
                        'taskdad' => $task->task->task->name ?? '',
                        'endDate' => $task['endDate'],
                        'startDate' => $task['startDate'],
                        'percentDone' => $task['percentDone']
                    ];
                }),
        );
    }
}
