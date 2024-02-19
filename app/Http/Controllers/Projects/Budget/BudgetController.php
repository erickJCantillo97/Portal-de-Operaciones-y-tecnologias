<?php

namespace App\Http\Controllers\Projects\Budget;

use App\Http\Controllers\Controller;
use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\Project\VirtualPep;
use App\Models\Projects\Project;
use Inertia\Inertia;

class BudgetController extends Controller
{
    public function index()
    {
        $projects = Project::active()->get();
        return Inertia::render('Project/Budget/Index', [
            'projects' => $projects,
        ]);
    }

    public function getDetailsBudget(Project $project)
    {
        $pepsPrincipales = Pep::where('project_id', $project->id)->first()->peps->where('identification', '<>', 'HITOS CONTRACTUALES');

        return response()->json([
            'peps' => $pepsPrincipales,
        ]);
    }

    public function getbudgetProject(Project $project)
    {
        $pep = Virtualpep::where('project_id', $project->id)->first();
        $labor = Pep::where('pep_id', $pep->id)->sum('labor');
        $materials = Pep::where('pep_id', $pep->id)->sum('materials');
        $services = Pep::where('pep_id', $pep->id)->sum('services');

        $materials_ejecutados = VirtualPep::where('project_id', $project->id)->get()->sum('materials_ejecutados') + Grafo::where('project_id', $project->id)->get()->sum('materials_ejecutados') + Operation::where('project_id', $project->id)->get()->sum('materials_ejecutados');

        $labor_ejecutados = VirtualPep::where('project_id', $project->id)->get()->sum('labor_ejecutados') + Grafo::where('project_id', $project->id)->get()->sum('labor_ejecutados') + Operation::where('project_id', $project->id)->get()->sum('labor_ejecutados');

        $services_ejecutados = VirtualPep::where('project_id', $project->id)->get()->sum('services_ejecutados') + Grafo::where('project_id', $project->id)->get()->sum('services_ejecutados') + Operation::where('project_id', $project->id)->get()->sum('services_ejecutados');
        return response()->json([
            'labor' => $labor,
            'materials' => $materials,
            'services' => $services,
            'total' => ($labor + $materials + $services),
            'materials_ejecutados' => $materials_ejecutados,
            'labor_ejecutados' => $labor_ejecutados,
            'serivices_ejecutados' => $services_ejecutados
        ]);
    }
}
