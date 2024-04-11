<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Project\ProgressProjectWeek;
use App\Models\Projects\Project;
use App\Models\VirtualTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardProjectsController extends Controller
{

    public function projectActive()
    {

        $tasks = VirtualTask::has('project')->whereNull('task_id')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'project_id' => $item->project->id,
                'avance' => number_format($item['percentDone'], 2),
                'name' => $item['name'],
                // 'file' => $item->project->contract->ship->file,
                'contrato' => $item->project->contract->name,
                'duracion' => $item->duration,
                'fechaI' => $item->startDate,
                'fechaF' => $item->endDate,
                'unidadDuracion' => $item->durationUnit,
                'costo' => $item->project->cost_sale
            ];
        });

        return response()->json([
            'projects' => $tasks
        ], 200);
    }

    public function getCPISPIPromedio()
    {
        $projects = Project::active();
        $projects_id = $projects->pluck('id')->toArray();
        $semanas = ProgressProjectWeek::where('real_progress', '<>', 0)->whereIn('project_id', $projects_id)->groupBy('project_id')->select('project_id', DB::raw("MAX(week) as week"))->get();
        $marks = [];
        foreach ($semanas as $semana) {
            $progre =  ProgressProjectWeek::where('project_id', $semana->project_id)->where('week', $semana->week)->first();
            
        }
    }
    public function getDataCPISPIPonderado()
    {
    }
}
