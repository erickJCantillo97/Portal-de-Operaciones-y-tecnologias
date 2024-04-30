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
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\Shift;
use App\Models\Views\DetailProjectWithCalendar;
use App\Models\Views\DetailScheduleTime;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Holidays\Countries\Colombia;
use Spatie\Holidays\Holidays;
use Illuminate\Support\Facades\Log;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

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
        $projectCalendars = DetailProjectWithCalendar::where('project_id', $project->id);
            $calendarInterval = $projectCalendars->select('calendarId', 'name')->distinct()->get()->map(function ($calendar) use($project) {
                return [
                    'id' => intval($calendar->calendarId),
                    'name' => $calendar->name,
                    'unspecifiedTimeIsWorking' => $calendar->unspecifiedTimeIsWorking == "1" ? true:false,
                    'intervals' => CalendarInterval::where('calendar_id', $calendar->calendarId)->get()->map(function ($interval) {
                        if ($interval->recurrentStartDate) {
                            return [
                                'recurrentStartDate' => $interval->recurrentStartDate,
                                'recurrentEndDate'   => $interval->recurrentEndDate,
                                'priority' => 20,
                                'isWorking' => $interval->isWorking == "1" ? true : false 
                            ];
                        }
                        return [
                            'startDate' => $interval->startDate,
                            'endDate'   => $interval->endDate,
                            'priority' => 30,
                            'isWorking'  => $interval->isWorking == "1" ?   true : false
                        ];
                    })->toArray()
                ];
            })->toArray();
            return response()->json([
                'success' => true,
                'project' =>  [
                    'calendar' => intval($project->calendar_id), // calendario por defecto
                    'startDate' => doubleval($project->startDate),
                    'hoursPerDay' => doubleval($project->hoursPerDay),
                    'daysPerWeek' => doubleval($project->daysPerWeek),
                    'daysPerMonth' => doubleval($project->daysPerMonth),
                    'durationUnit'=>'day'

                ],
                'calendars' => [
                    "rows" => $calendarInterval
                ],
                'tasks' => ['rows' => Task::where('project_id', $project->id)->whereNull('task_id')->orderBy('parentIndex')->get()],
                'dependencies' => ['rows' => Dependecy::get()],
                'resources' => ['rows' => $recursos],
                'assignments' => ['rows' => Assignment::get()],
                'timeRanges' => ['rows' => []],
                'shift' => Shift::whereNull('user')->select('id', 'name')->get()->toArray(),
                'assignCalendar' => Calendar::all()->select('id', 'name')->toArray()
            ]);
        
    }

    public function beforeSync (Request $request, Project $project){
        try {
            DB::beginTransaction();
                $calendarSave = Calendar::firstOrCreate([
                    'expanded' => $request->calendar['expanded'],
                    'version' => $request->calendar['version'],
                    'name' => $request->calendar['name'],
                    'unspecifiedTimeIsWorking' => $request->calendar['unspecifiedTimeIsWorking'],
                ]);
                $calendarSave->save();
                if (isset($request->calendar['intervals'])) {
                    foreach ($request->calendar['intervals'] as $intervals) {
                        CalendarInterval::firstOrCreate([
                            'calendar_id' => $calendarSave->id,
                            'isWorking' => $intervals['isWorking'],
                            'priority' => $intervals['priority'],
                            'recurrentStartDate' => isset($intervals['recurrentStartDate']) == true ?  $intervals['recurrentStartDate'] : '',
                            'recurrentEndDate' => isset($intervals['recurrentEndDate'])  == true ? $intervals['recurrentEndDate'] : '',
                            'startDate' => isset($intervals['startDate']) == true ? Carbon::parse($intervals['startDate'])->format('Y-m-d H:i') : null,
                            'endDate' => isset($intervals['endDate']) == true ? Carbon::parse($intervals['endDate'])->format('Y-m-d H:i') : null
                        ]);
                    }
            }
            ProjectWithCalendar::firstOrCreate([
                'project_id' => $project->id,
                'calendar_id' => $calendarSave->id
            ]);
            $project->calendar_id = $calendarSave->id;
            $project->save();
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Calendario asignado al proyecto correctamente', 'calendarId'=>$calendarSave->id]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => $e->getMessage()]);
        }
    }

    public function sync(Project $project, Request $request)
    {
        $complete = true;
        $mensaje = 'Acción realizada';
        $rows = [];
        $removed = [];
        $rowsDependecy = [];
        $removedDependecy = [];
        $assgimentRows = [];
        // $removedAssignments = [];
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
                    'manuallyScheduled' => $task['manuallyScheduled'],
                    'parentIndex' => intval($task['parentIndex'])
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
                    'parentIndex' => $task['parentIndex'] ?? intval($taskUpdate->parentIndex),
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
            DB::beginTransaction();
            $now = Carbon::now();
            $task = VirtualTask::find($request->assignments['added'][0]['event']);
            if (Carbon::parse($task->endDate)->lt($now->format('Y-m-d'))) {
                foreach ($request->assignments['added'] as $assignment) {
                    $employee = searchEmpleados('Num_SAP', $assignment['resource'])->first();
                    $assigmmentCreate = Assignment::firstOrCreate([
                        'event' => $assignment['event'],
                        'resource' => $assignment['resource'],
                        'units' => $assignment['units'],
                        'name' => $employee->Nombres_Apellidos,
                        'costo_hora' => $employee->Costo_Hora,
                    ]);
                    array_push($assgimentRows, [
                        '$PhantomId' => end($assignment),
                        'id' => $assigmmentCreate['id'],
                        'added_dt' => $assigmmentCreate->created_at,
                    ]);
                }
                $mensaje = 'Recurso Agregado';
            } else {
                if (count(VirtualTask::where('task_id', $request->assignments['added'][0]['event'])->get()) > 0) {
                    $complete = false;
                    $mensaje = 'No se puede programar en esta actividad porque no es de ultimo nivel';
                }
                if ($now->lt(Carbon::parse($task->startDate))) {
                    $now = Carbon::parse($task->startDate);
                }
                foreach ($request->assignments['added'] as $assignment) {
                    if (is_numeric($assignment['resource'])) {
                        $employee = searchEmpleados('Num_SAP', $assignment['resource'])->first();
                        do {
                            if (getWorkingDays($now)) {
                                $exist = DetailScheduleTime::where('idUsuario', $assignment['resource'])
                                    ->where('fecha', $now)->get();
                                if (count($exist) > 0) {
                                    foreach ($exist as $details) {
                                        disprogramming($details->idSchedule);
                                    }
                                }
                                programming(
                                    $now,
                                    $assignment['resource'],
                                    $task->project->shiftObject->startShift,
                                    $task->project->shiftObject->endShift,
                                    $assignment['event'],
                                    $employee->Nombres_Apellidos,
                                    $employee->Costo_Hora
                                );
                            }
                            $now = $now->addDays(1);
                        } while ($now->lte(Carbon::parse($task->endDate)));
                        if (Carbon::now()->lt(Carbon::parse($task->startDate))) {
                            $now = Carbon::parse($task->startDate);
                        } else {
                            $now = Carbon::now();
                        }
                        $assigmmentCreate = Assignment::firstOrCreate([
                            'event' => $assignment['event'],
                            'resource' => $assignment['resource'],
                            'units' => $assignment['units'],
                            'name' => $employee->Nombres_Apellidos,
                            'costo_hora' => $employee->Costo_Hora,
                        ]);
                        array_push($assgimentRows, [
                            '$PhantomId' => end($assignment),
                            'id' => $assigmmentCreate['id'],
                            'added_dt' => $assigmmentCreate->created_at,
                        ]);
                    }
                }
            }
            DB::commit();
        }
        if (isset($request->assignments['removed'])) {
            DB::beginTransaction();
            foreach ($request->assignments['removed'] as $id) {
                $assignment = Assignment::find($id)->first();
                $deleteIds = Schedule::where('employee_id', $assignment->resource)
                    ->where('task_id', $assignment->event)
                    ->pluck('id')->toArray();
                ScheduleTime::whereIn('schedule_id', $deleteIds)->delete();
                Schedule::where('employee_id', $assignment->resource)
                    ->where('task_id', $assignment->event)
                    ->delete();
                $assignment->delete();
                //    array_push($removedAssignments, ['id' => $id]);
            }
            DB::commit();
        }
        if (isset($request->calendars['added'])) {
            //return dd($request->calendars['added']);
            FacadesDB::beginTransaction();
            foreach ($request->calendars['added'] as $calendar) {
                $calendarsDetails = [];
                $calendarSave = Calendar::firstOrCreate([
                    'expanded' => $calendar['expanded'],
                    'version' => $calendar['version'],
                    'name' => $calendar['name'],
                    'unspecifiedTimeIsWorking' => $calendar['unspecifiedTimeIsWorking'],
                ]);
                $calendarSave->save();
                if (isset($calendar['intervals']['added'])) {
                    foreach ($calendar['intervals']['added'] as $intervals) {
                        CalendarInterval::firstOrCreate([
                            'calendar_id' => $calendarSave->id,
                            'isWorking' => $intervals['isWorking'],
                            'priority' => $intervals['priority'],
                            'recurrentStartDate' => isset($intervals['recurrentStartDate']) == true ?  $intervals['recurrentStartDate'] : '',
                            'recurrentEndDate' => isset($intervals['recurrentEndDate'])  == true ? $intervals['recurrentEndDate'] : '',
                            'startDate' => isset($intervals['startDate']) == true ? Carbon::parse($intervals['startDate'])->format('Y-m-d H:i') : null,
                            'endDate' => isset($intervals['endDate']) == true ? Carbon::parse($intervals['endDate'])->format('Y-m-d H:i') : null
                        ]);
                        array_push($calendarsDetails, [
                            'id' => $intervals['$PhantomId'],
                            'recurrentStartDate' => isset($intervals['recurrentStartDate']) == true ? $intervals['recurrentStartDate'] : '',
                            'recurrentEndDate' => isset($intervals['recurrentEndDate'])  == true ? $intervals['recurrentEndDate'] : '',
                            'isWorking' => $intervals['isWorking']
                        ]);
                    }
                }
                array_push($calendars, [
                    'id' => $calendar['$PhantomId'],
                    'name' => $calendar['name'],
                    'intervals' => $calendarsDetails
                ]);
                ProjectWithCalendar::firstOrCreate([
                    'project_id' => $project->id,
                    'calendar_id' => $calendarSave->id
                ]);
            }
            // return dd($calendars);
            DB::commit();
        }
        if (isset($request->project['added'])) {
            $project->calendar_id = $request->project['added']['calendar'];
            $project->daysPerMonth = $request->project['added']['daysPerMonth'];
            $project->direction = $request->project['added']['direction'];
            $project->daysPerWeek = $request->project['added']['daysPerWeek'];
            $project->hoursPerDay = $request->project['added']['hoursPerDay'];
            $project->startDate = $request->project['added']['startDate'];
            $project->update();
        }
        return response()->json([
            'success' => $complete,
            'mensaje' => $mensaje,
            'requestId' => $request->requestId,
            'tasks' => [
                'rows' => $rows,
                'removed' => $removed,
            ],
            'assignments' => [
                'rows' => $assgimentRows,
                //'removed' => $removedAssignments,
            ],
            'dependencies' => [
                'rows' => $rowsDependecy,
                'removed' => $removedDependecy,
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

    public function assignmentCalendar(Request $request)
    {
        try {
            DB::beginTransaction();
            $days = 'on ';
            if ($request->newCalendar) {
                $shift = Shift::find($request->shift['id']);
                foreach ($request->days as $day) {
                    $days = $days . $day['code'] . ',';
                }
                $days = substr($days, 0, strlen($days) - 1);
                $daysEnd = $days;
                $days = $days . " at " . Carbon::parse($shift->startShift)->format('H:i');
                $daysEnd =  $daysEnd . " at " . Carbon::parse($shift->endShift)->format('H:i');
                $calendar = Calendar::create([
                    'expanded' => 1,
                    'version' => 2,
                    'name' => $request->name,
                    'unspecifiedTimeIsWorking' => count($request->isWorking) > 0 ? false : true
                ]);
                $calendar->save();
                CalendarInterval::create([
                    'calendar_id' => $calendar->id,
                    'isWorking' => count($request->isWorking) > 0 ? false : true,
                    'priority' => 20,
                    'recurrentEndDate' => $daysEnd,
                    'recurrentStartDate' => $days
                ]);
                $project = Project::find($request->project);
                $project->calendar_id = $calendar->id;
                $project->save();
            } else {
                $project = Project::find($request->project);
                $project->calendar_id = $request->calendar['id'];
                $project->save();
            }
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Calendario agregado']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => $e->getMessage()]);
        }
    }
}
