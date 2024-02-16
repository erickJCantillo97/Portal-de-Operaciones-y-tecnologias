<?php

namespace App\Http\Controllers\Projects\Budget;

use App\Http\Controllers\Controller;
use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\Project\VirtualPep;
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
        ]);
    }

    public function getbudgetProject(Project $project)
    {
        $pep = Virtualpep::where('project_id', $project->id)->first();
        $labor_ejecutado = VirtualPep::where('project_id', $project->id)->get()->sum('services_ejecutados') + Grafo::where('project_id', $project->id)->get()->sum('services_ejecutados') + Operation::where('project_id', $project->id)->get()->sum('services_ejecutados');
        $labor = Pep::where('pep_id', $pep->id)->sum('labor');
        $materials = Pep::where('pep_id', $pep->id)->sum('materials');
        $services = Pep::where('pep_id', $pep->id)->sum('services');
        dd($labor_ejecutado);
        return response()->json([
            'labor' => $labor,
            'materials' => $materials,
            'services' => $services,
            'total' => ($labor + $materials + $services)
        ]);
    }
}
