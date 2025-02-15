<?php

namespace App\Http\Controllers\Projects\Budget;

use App\Http\Controllers\Controller;
use App\Imports\Budge\BudgetImport;
use App\Imports\Budge\EstructureImport;
use App\Imports\Budge\ExecutedImport;
use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\Project\VirtualPep;
use App\Models\Projects\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

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
        if (!$pep) {
            return response()->json([
                'labor' => 0,
                'materials' => 0,
                'services' => 0,
                'total' => 0,
                'materials_ejecutados' => 0,
                'labor_ejecutados' => 0,
                'services_ejecutados' => 0,
            ]);
        }
        $labor = Pep::where('pep_id', $pep->id)->sum('labor');
        $materials = Pep::where('pep_id', $pep->id)->sum('materials');
        $services = Pep::where('pep_id', $pep->id)->sum('services');

        $materials_ejecutados = Operation::where('project_id', $project->id)->get()->sum('materials_ejecutados');

        $labor_ejecutados = Operation::where('project_id', $project->id)->get()->sum('labor_ejecutados');

        $services_ejecutados = Operation::where('project_id', $project->id)->get()->sum('services_ejecutados');
        return response()->json([
            'labor' => $labor,
            'materials' => $materials,
            'services' => $services,
            'total' => ($labor + $materials + $services),
            'materials_ejecutados' => $materials_ejecutados,
            'labor_ejecutados' => $labor_ejecutados,
            'services_ejecutados' => $services_ejecutados,
            'total_ejecutado' => ($materials_ejecutados + $labor_ejecutados + $services_ejecutados)
        ]);
    }


    public function uploadEstructure(Request $request, $project)
    {
        try {
            // dd($request->files->get('files'));
            Excel::import(new EstructureImport($project), $request->docs);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
    }

    public function uploadGudget(Request $request, $project)
    {
        try {
            Excel::import(new BudgetImport($project), $request->docs);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
    }

    public function executedimport(Request $request, $project)
    {
        try {
            Pep::where('project_id', $project)->update([
                'materials_ejecutados' => 0,
                'labor_ejecutados' => 0,
                'services_ejecutados' => 0
            ]);
            Grafo::where('project_id', $project)->update([
                'materials_ejecutados' => 0,
                'labor_ejecutados' => 0,
                'services_ejecutados' => 0
            ]);
            Operation::where('project_id', $project)->update([
                'materials_ejecutados' => 0,
                'labor_ejecutados' => 0,
                'services_ejecutados' => 0
            ]);

            Excel::import(new ExecutedImport($project), $request->docs);
            return back()->withErrors(session('grafos_errors'));
            // dd(session('grafos_errors'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
    }
}
