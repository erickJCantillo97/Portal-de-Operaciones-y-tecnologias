<?php

namespace App\Http\Controllers;

use App\Models\Gantt\Assignment;
use App\Models\Gantt\Dependecy;
use App\Models\Gantt\Task;
use App\Models\Labor;
use App\Models\Projects\Calendar;
use App\Models\Projects\CalendarInterval;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectWithCalendar;
use App\Models\Views\DetailProjectWithCalendar;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Holidays\Countries\Colombia;
use Spatie\Holidays\Holidays;
use Illuminate\Support\Facades\Log;
use DB;

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
        // $holidays = collect(Holidays::for(Colombia::make())->get())->map(function ($day) {

        //     return [
        //         "startDate" => $day['date'],
        //         "endDate" => $day['date'],
        //         "isWorking" => false,
        //         'name' => $day['name']
        //     ];
        // });

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
        $defaultCalendar = Calendar::find($project->calendar_id);
        $recursos = array_merge_recursive($cargos, $personal);
        $projectCalendars = DetailProjectWithCalendar::where('project_id',$project->id);
        if($projectCalendars->count() > 0){    
        $calendarInterval = $projectCalendars->select('calendarId','name')->distinct()->get()->map(function ($calendar){
            return [
                'id' => $calendar->calendarId,
                'name' => $calendar->name,
                'intervals'=>CalendarInterval::where('calendar_id',$calendar->calendarId)->get()->map(function ($interval){
                    return[
                        'recurrentStartDate' => $interval->recurrentStartDate,
                        'recurrentEndDate'   => $interval->recurrentEndDate,
                        'isWorking'          => $interval->isWorking == "1" ? true: false
                    ];
                })->toArray()
            ];
        })->toArray();
        return response()->json([
            'success' => true,
            'proyect' => ['rows' => [
                'calendar' => $defaultCalendar == null ? '': $defaultCalendar->id, // calendario por defecto
                'startDate'=> $project->startDate,
                'hoursPerDay'=> $project->hoursPerDay,
                'daysPerWeek'=> $project->daysPerWeek,
                'daysPerMonth'=> $project->daysPerMonth

            ]],
            'calendars' => [
                "rows" => $calendarInterval
            ],
            'tasks' => ['rows' => Task::where('project_id', $project->id)->whereNull('task_id')->get()],
            'dependencies' => ['rows' => Dependecy::get()],
            'resources' => ['rows' => $recursos],
            'assignments' => ['rows' => Assignment::get()],
            'timeRanges' => ['rows' => []],
        ]);
    }else{
        return response()->json([
            'success' => true,
            'proyect' => [],
            'calendars' => [
                "rows" => []
            ],
            'tasks' => ['rows' => Task::where('project_id', $project->id)->whereNull('task_id')->get()],
            'dependencies' => ['rows' => Dependecy::get()],
            'resources' => ['rows' => $recursos],
            'assignments' => ['rows' => Assignment::get()],
            'timeRanges' => ['rows' => []],
        ]);
    }
    }

    public function sync(Project $project, Request $request)
    {
        $rows = [];
        $removed = [];
        $rowsDependecy = [];
        $removedDependecy = [];
        $assgimentRows = [];
        $calendars = [];
        $calendarsDetails = [];
        //return dd($request->calendars['added']);
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
                $dependency = Dependecy::create([
                    'from' => $dependency['from'],
                    'to' => $dependency['to'],
                    'type' => $dependency['type'],
                    'lag' => $dependency['lag'],
                    'lagUnit' => $dependency['lagUnit'],
                ]);
                array_push($rowsDependecy, [
                    '$PhantomId' => $dependency['$PhantomId'],
                    'id' => $dependency->id,
                    'added_dt' => $dependency->created_at,
                ]);
            }
        }
        if (isset($request->dependencies['removed'])) {
            foreach ($request->dependencies['removed'] as $dependency) {
                $taskUpdate = Dependecy::where('id', $dependency['id'])->delete();
                array_push($removedDependecy, ['id' => $dependency['id']]);
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
        if(isset($request->calendars['added'])){
            //return dd($request->calendars['added']);
            DB::beginTransaction();
            foreach ($request->calendars['added'] as $calendar){
                $calendarsDetails = [];
                $calendarSave = Calendar::firstOrCreate([
                    'expanded'=>$calendar['expanded'],
                    'version'=>$calendar['version'],
                    'name'=>$calendar['name'],
                    'unspecifiedTimeIsWorking'=>$calendar['unspecifiedTimeIsWorking'],
                ]);
                $calendarSave->save();
                if(isset($calendar['intervals']['added'])){
                foreach ($calendar['intervals']['added'] as $intervals) {
                    CalendarInterval::firstOrCreate([
                        'calendar_id'=>$calendarSave->id,
                        'isWorking'=>$intervals['isWorking'],
                        'priority'=>$intervals['priority'],
                        'recurrentStartDate'=>isset($intervals['recurrentStartDate'])== true ?  $intervals['recurrentStartDate'] : '',
                        'recurrentEndDate'=>isset($intervals['recurrentEndDate'])  == true ? $intervals['recurrentEndDate'] : '',
                        'startDate'=>isset($intervals['startDate'])== true ? Carbon::parse($intervals['startDate'])->format('Y-m-d H:i') : null,
                        'endDate'=>isset($intervals['endDate'])== true ? Carbon::parse($intervals['endDate'])->format('Y-m-d H:i') : null
                    ]);
                    array_push($calendarsDetails, [
                                        'id'=>$intervals['$PhantomId'],
                                        'recurrentStartDate'=>isset($intervals['recurrentStartDate']) == true ? $intervals['recurrentStartDate'] : '',
                                        'recurrentEndDate'=>isset($intervals['recurrentEndDate'])  == true ? $intervals['recurrentEndDate'] : '',
                                        'isWorking'=>$intervals['isWorking']
                                ]);
                }
            }
                array_push($calendars, [
                    'id' => $calendar['$PhantomId'],
                    'name' => $calendar['name'],
                    'intervals'=>$calendarsDetails
                ]);
                ProjectWithCalendar::firstOrCreate([
                    'project_id'=>$project->id,
                    'calendar_id'=>$calendarSave->id
                ]);
            }
            // return dd($calendars);
            DB::commit();
        }
        if(isset($request->project['added'])){
            $project->calendar_id = $request->project['added']['calendar'];
            $project->daysPerMonth = $request->project['added']['daysPerMonth'];
            $project->direction = $request->project['added']['direction'];
            $project->daysPerWeek = $request->project['added']['daysPerWeek'];
            $project->hoursPerDay = $request->project['added']['hoursPerDay'];
            $project->startDate = $request->project['added']['startDate'];
            $project->update();
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
            'dependencies' => [
                'rows' => $rowsDependecy,
                'removed' => $removedDependecy,
            ],
            'project'=>[
                'calendar'=> $project->id,
                'startDate'=> $project->startDate,
                'hoursPerDay'=> $project->hoursPerDay,
                'daysPerWeek'=> $project->daysPerWeek,
                'daysPerMonth'=> $project->daysPerMonth
            ],
            'calendars' => [
                'rows' => $calendars
            ]
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
