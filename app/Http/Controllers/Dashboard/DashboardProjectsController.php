<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\VirtualTask;
use Illuminate\Http\Request;

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
}
