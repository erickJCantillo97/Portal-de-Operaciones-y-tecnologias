<?php

namespace App\Http\Controllers;

use App\Models\Gantt\Assignment;
use App\Models\Gantt\Dependecy;
use App\Models\Gantt\Task;
use App\Models\Labor;
use App\Models\Projects\Project;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function create(Project $project)
    {

        return Inertia::render('Project/Schedule/Schedule', [
            'project' => $project,
        ]);
    }

    public function index(Project $project)
    {
        return Inertia::render('Project/Schedule/Schedule', [
            'project' => $project->id,
        ]);
    }

    public function import(Project $project, Request $request)
    {
        return Inertia::render('GanttImporter', [
            'project' => $project
        ]);
    }

    public function get(Project $project)
    {

        return response()->json([
            'success' => true,
            'proyect' => ['rows' => [
                'calendar' => 'general',
                'startDate' => '2022-05-31',
                'hoursPerDay' => 9,
                'daysPerWeek' => 5,
                'daysPerMonth' => 20,
            ]],
            'tasks' => ['rows' => Task::where('project_id', $project->id)->whereNull('task_id')->get()],
            'dependencies' => ['rows' => Dependecy::get()],
            'resources' => ['rows' => Labor::where('gerencia', auth()->user()->gerencia)->get()->map(function ($cargo) {
                return [
                    'id' => $cargo->id,
                    'name' => $cargo->name,
                    'costo_hora' => '$ ' . number_format($cargo->costo_hora, 0),
                ];
            })],
            'assignments' => ['rows' => Assignment::get()],
            'timeRanges' => ['rows' => []],
        ]);
    }

    public function sync(Project $project, Request $request)
    {
        $rows = [];
        $removed = [];
        $assgimentRows = [];
        if (isset($request->tasks['added'])) {
            foreach ($request->tasks['added'] as $task) {
                if (!isset($task['parentID'])) {
                    $parentID = null;
                } else {
                    $parentID = is_numeric($task['parentId']) ? $parentID = Task::orWhere('id', $task['parentId'])->first()->id : $parentID = Task::where('PhantomId', $task['parentId'])->first()->id;
                }
                $taskCreate = Task::create([
                    'project_id' => $project->id,
                    'name' => $task['name'],
                    'task_id' => $parentID,
                    'PhantomId' => $task['$PhantomId'],
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
                // dd($taskUpdate);
            }
        }
        if (isset($request->tasks['removed'])) {
            foreach ($request->tasks['removed'] as $task) {
                $taskUpdate = Task::where('id', $task['id'])->delete();
                array_push($removed, ['id' => $task['id']]);
            }
        }
        if (isset($request->dependencies['added'])) {
            foreach ($request->dependencies['added'] as $dependency) {
                Dependecy::create([
                    'from' =>  $dependency['from'],
                    'to' => $dependency['to'],
                    'type' => $dependency['type'],
                    'lag' => $dependency['lag'],
                    'lagUnit' => $dependency['lagUnit'],
                ]);
            }
        }
        if (isset($request->assignments['added'])) {
            foreach ($request->assignments['added'] as $assignment) {
                $labor = Labor::find($assignment['resource']);
                $assigmmentCreate = Assignment::create([
                    'event' => $assignment['event'],
                    'resource' => $assignment['resource'],
                    'units' => $assignment['units'],
                    'name' => $labor->name,
                    'costo_hora' => $labor->costo_hora,
                ]);
                array_push($assgimentRows, [
                    '$PhantomId' => end($assignment),
                    'id' => $assigmmentCreate['id'],
                    'added_dt' => $assigmmentCreate->created_at,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'requestId' => $request->requestId,
            'tasks' => [
                'rows' => $rows,
                'removed' => $removed,
            ],
            'assignments' => [
                'rows' => $assgimentRows,
            ],
        ]);
    }

    public function syncImporter(Project $project, Request $request)
    {
        return response()->json([
            'success' => true,
            'requestId' => $request->requestId,
        ]);
    }

    public function wizard(Project $project)
    {

        return Inertia::render('Project/Schedule/CreateSchedule', [
            'project' => $project,
            'groups' => gruposConstructivos(),
        ]);
    }

    public function getAssignmentsTask(VirtualTask $task)
    {
        return response()->json([
            'assignments' => Assignment::where('event', $task->id)->get(),
        ]);
    }
}
