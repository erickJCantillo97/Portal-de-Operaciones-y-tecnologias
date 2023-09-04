<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Gantt\Task;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function get()
    {

        return response()->json([
            "success"=> true,
            "proyect"=>['rows' => [
                "calendar"=> "general",
                "startDate"=> "2022-05-31",
                "hoursPerDay"=> 9,
                "daysPerWeek"=> 5,
                "daysPerMonth"=> 20
            ]],
            "tasks" => ['rows' => Task::whereNull('task_id')->get()],
            "dependencies" => ['rows' => []],
            "resources" => ['rows' => []],
            "assignments" => ['rows' => []],
            "timeRanges" => ['rows' => []]
        ]);
    }

    public function sync(Request $request)
    {
        $rows = [];
        $removed = [];
        // dd($request->requestId);
        if(isset($request->tasks['added'])){
            foreach($request->tasks['added'] as $task){
                $taskCreate = Task::create([
                    'name' => $task['name'],
                    'task_id' => $task['parentId'],
                    'percentDone' => $task['percentDone'],
                    'duration' => $task['duration'],
                    'startDate' => $task['startDate'],
                    'endDate' => $task['endDate'],
                ]);
                array_push($rows , [
                    '$PhantomId' => end($task),
                    'id' => $taskCreate['id'],
                    'added_dt' => $taskCreate->created_at,
                ]);
            }
        }
        if(isset($request->tasks['updated'])){
            foreach($request->tasks['updated'] as $task){
                $taskUpdate = Task::where('id', $task['id'])->first();
                $taskUpdate->update([
                    'name' => $task['name'] ?? $taskUpdate->name,
                    'task_id' => $task['parentId'] ?? $taskUpdate->task_id,
                    'percentDone' => $task['percentDone'] ?? $taskUpdate->percentDone,
                    'duration' => $task['duration'] ?? $taskUpdate->duration,
                    'startDate' => $task['startDate'] ?? $taskUpdate->startDate,
                    'endDate' => $task['endDate'] ?? $taskUpdate->endDate,
                ]);
            }
        }
        if(isset($request->tasks['removed'])){
            foreach($request->tasks['removed'] as $task){
                $taskUpdate = Task::where('id', $task['id'])->delete();
                array_push($removed, ['id' => $task['id']]);
            }
        }



        return response()->json([
            "success"   => true,
            'requestId' => $request->requestId , 'tasks' => [
            'rows' => $rows,
            'removed' => $removed
        ]]);
    }
}
