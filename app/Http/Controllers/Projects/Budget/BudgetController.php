<?php

namespace App\Http\Controllers\Projects\Budget;

use App\Http\Controllers\Controller;
use App\Models\Project\Pep;
use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BudgetController extends Controller
{
    public function index()
    {
        $projects = Project::active()->get();

        return Inertia::render('Project/Budget/Index', [
            'projects' => $projects
        ]);
    }

    public function getDetailsBudget(Project $project)
    {
        $pepsPrincipales = Pep::where('project_id', $project->id)->first()->peps->where('identification', '<>', 'HITOS CONTRACTUALES');

        return response()->json([
            'peps' => $pepsPrincipales,
            'presupuestos' => [
                'materiales' => $pepsPrincipales->sum('materials'),
                'mdo' => $pepsPrincipales->sum('labor'),
                'servicios' => $pepsPrincipales->sum('services')
            ],
            'ejecutado' => [
                'materiales' => $pepsPrincipales->sum('materials_ejecutados'),
                'mdo' => $pepsPrincipales->sum('labor_ejecutados'),
                'servicios' => $pepsPrincipales->sum('services_ejecutados')
            ],
        ]);
    }
}
