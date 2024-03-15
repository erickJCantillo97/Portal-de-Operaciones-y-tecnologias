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
        // $taskProject = Task::where('project_id', $project->id)->first();
        // if (!$taskProject) {
        //     Task::firstOrcreate([
        //         'project_id' => $project->id,
        //         'name' => $project->name,
        //         'percentDone' => 0,
        //         'duration' => Carbon::parse($project->start_date)->diffInDays($project->end_date),
        //         'durationUnit' => 'Days',
        //         'startDate' => $project->start_date,
        //         'endDate' => $project->end_date,
        //     ]);
        // }
        return Inertia::render('Project/Schedule/Schedule', [
            'project' => Project::where('id', $project->id)->first(),

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
        $project->programming_resources = 'CARGO';
        $personal = getPersonalUser()->map(function ($p) {
            return [
                'id' => $p['Num_SAP'],
                'name' => $p['Nombres_Apellidos'],
                'costo_hora' => '$ ' . number_format($p['Costo_Hora'], 0)
            ];
        })->toArray();
        $cargos =  Labor::where('gerencia', auth()->user()->gerencia)->get()->map(function ($cargo) {
            return [
                'id' => 'CA' . $cargo->id,
                'name' => $cargo->name,
                'costo_hora' => '$ ' . number_format($cargo->costo_hora, 0),
            ];
        })->toArray();
        $recursos = array_merge_recursive($cargos, $personal);

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
            'resources' => ['rows' => $recursos],
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
                if (!isset($task['parentId'])) {
                    $parentID = null;
                } else {
                    $parentID = Task::Where('id', $task['parentId'])->first()->id ?? null;
                    foreach ($rows as $row) {
                        if ($row['$PhantomId'] == $task['parentId']) {
                            $parentID = $row['id'];
                            break;
                        }
                    }
                    // $parentID = is_numeric($task['parentId']) ? Task::Where('id', $task['parentId'])->first()->id : Task::where('PhantomId', $task['parentId'])->first()->id;
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
                    'manuallyScheduled' => $task['manuallyScheduled']
                ]);
                array_push($rows, [
                    '$PhantomId' => $task['$PhantomId'],
                    'id' => $taskCreate->id,
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
                    'executor' => $task['executor'] ?? $taskUpdate->executor,
                    'manager' => $task['manager'] ?? $taskUpdate->manager,
                    'manuallyScheduled' => $task['manuallyScheduled'] ?? $taskUpdate->manuallyScheduled,
                ]);
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
                if (count($rows) > 0) {
                    $collections = collect($rows);

                    $dependency['from'] =  $collections->where('$PhantomId', $dependency['from'])->first()['id'];

                    $dependency['to'] =  $collections->where('$PhantomId', $dependency['to'])->first()['id'];
                }
                Dependecy::create([
                    'from' => $dependency['from'],
                    'to' => $dependency['to'],
                    'type' => $dependency['type'],
                    'lag' => $dependency['lag'],
                    'lagUnit' => $dependency['lagUnit'],
                ]);
            }
        }
        if (isset($request->assignments['added'])) {
            foreach ($request->assignments['added'] as $assignment) {
                if (!is_numeric($assignment['resource'])) {
                    $idResource = str_replace('CA', '', $assignment['resource']);
                    $labor = Labor::find($idResource);
                } else {
                    $labor = searchEmpleados('Num_SAP', $assignment['resource']);
                    $labor->name = $labor['Nombres_Apellidos'];
                }
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
