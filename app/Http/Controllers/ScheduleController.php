<?php

namespace App\Http\Controllers;

use App\Models\Gantt\Task;
use App\Models\Labor;
use App\Models\Projects\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function create(Project $project)
    {
        $groups = gruposConstructivos();
        $taskProject = Task::where('project_id', $project->id)->first();
        if(!$taskProject){
            $taskCreate = Task::firstOrcreate([
            'project_id' => $project->id,
            'name' => $project->name,
            'percentDone' => 0,
            'duration' => Carbon::parse($project->start_date)->diffInDays($project->end_date),
            'durationUnit' => 'Days',
            'startDate' => $project->start_date,
            'endDate' => $project->end_date,
        ]);
        }

        return Inertia::render('GanttBryntum', [
            'project' => $project->id,
            'groups' => $groups,
        ]);
    }

    public function index(Project $project)
    {
        return Inertia::render('GanttBryntum', [
            'project' => $project
        ]);
    }
    public function import(Request $request)
    {
        return Inertia::render('GanttImporter', [

        ]);
    }

    public function get(Project $project)
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
            "resources" => ['rows' => Labor::where('gerencia', auth()->user()->gerencia)->get()->map(function($cargo){
                return [
                    'id' => $cargo->id,
                    'name' => $cargo->name,
                    'costo_hora' => '$ '. number_format($cargo->costo_hora, 0),
                ];
            })],
            "assignments" => ['rows' => []],
            "timeRanges" => ['rows' => []]
        ]);
    }

    public function sync(Project $project,Request $request)
    {
        $rows = [];
        $removed = [];
        // dd($request);
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

    public function wizard(Project $project){

        return Inertia::render('Project/Schedule/CreateSchedule', [
            'project' => $project,
            'groups'=> gruposConstructivos()
        ]);
    }
}
