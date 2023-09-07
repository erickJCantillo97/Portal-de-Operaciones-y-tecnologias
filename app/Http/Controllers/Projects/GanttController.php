<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Gantt\Task;
use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GanttController extends Controller
{
<<<<<<< HEAD
    public function index(Request $request)
    {
        return Inertia::render('GanttBryntum', [
            'project' => Project::find($request->id)
        ]);
    }

    public function import(Request $request)
    {
        return Inertia::render('GanttImporter', [
            'project' => Project::find($request->id)
        ]);
    }

    public function get()
=======
    public function get(Project $project)
>>>>>>> 35106b40e89ef0836949407e9ec275b6e27c7c1b
    {

        return response()->json([
            "success" => true,
            "proyect" => ['rows' => [
                "calendar" => "general",
                "startDate" => "2022-05-31",
                "hoursPerDay" => 9,
                "daysPerWeek" => 5,
                "daysPerMonth" => 20
            ]],
            "tasks" => ['rows' => Task::where('project_id', $project->id)->whereNull('task_id')->get()],
            "dependencies" => ['rows' => []],
            "resources" => ['rows' => []],
            "assignments" => ['rows' => []],
            "timeRanges" => ['rows' => []]
        ]);
    }

    public function sync(Project $project,Request $request)
    {
        $rows = [];
        $removed = [];
        // dd($request->requestId);
        if (isset($request->tasks['added'])) {
            foreach ($request->tasks['added'] as $task) {
                $taskCreate = Task::create([
                    'project_id' => $project->id,
                    'name' => $task['name'],
                    'task_id' => $task['parentId'],
                    'percentDone' => $task['percentDone'],
                    'duration' => $task['duration'],
                    'durationUnit' => $task['durationUnit'],
                    'startDate' => $task['startDate'],
                    'endDate' => $task['endDate'],
                ]);
                array_push($rows, [
                    '$PhantomId' => end($task),
                    'id' => $taskCreate['id'],
                    'added_dt' => $taskCreate->created_at,
                ]);
            }
        }
        if (isset($request->tasks['updated'])) {
            foreach ($request->tasks['updated'] as $task) {
                $taskUpdate = Task::where('id', $task['id'])->first();
                $taskUpdate->update([
                    'name' => $task['name'] ?? $taskUpdate->name,
                    'task_id' => $task['parentId'] ?? $taskUpdate->task_id,
                    'percentDone' => $task['percentDone'] ?? $taskUpdate->percentDone,
                    'durationUnit' => $task['durationUnit'] ?? $taskUpdate->durationUnit,
                    'duration' => $task['duration'] ?? $taskUpdate->duration,
                    'startDate' => $task['startDate'] ?? $taskUpdate->startDate,
                    'endDate' => $task['endDate'] ?? $taskUpdate->endDate,
                ]);
            }
        }
        if (isset($request->tasks['removed'])) {
            foreach ($request->tasks['removed'] as $task) {
                $taskUpdate = Task::where('id', $task['id'])->delete();
                array_push($removed, ['id' => $task['id']]);
            }
        }



        return response()->json([
            "success"   => true,
            'requestId' => $request->requestId, 'tasks' => [
                'rows' => $rows,
                'removed' => $removed
            ]
        ]);
    }

    public function syncImporter(Request $request)
    {

        return response()->json([
            "success"   => true,
            'requestId' => $request->requestId,
        ]);
    }
}
