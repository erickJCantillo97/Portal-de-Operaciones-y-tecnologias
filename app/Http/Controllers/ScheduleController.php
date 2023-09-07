<?php

namespace App\Http\Controllers;

use App\Models\Gantt\Task;
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
}
