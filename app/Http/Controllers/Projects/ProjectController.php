<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project\ProgressProjectWeek;
use App\Models\Project\WeekTask;
use App\Models\Projects\Authorization;
use App\Models\Projects\Contract;
use App\Models\Projects\Milestone;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectsShip;
use App\Models\Projects\Ship;
use App\Models\Quotes\Quote;
use App\Models\VirtualTask;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::with('contract')->orderBy('created_at', 'DESC')->get();
        if ($request->expectsJson()) {
            return response()->json(['projects' => $projects]);
        }

        return Inertia::render('Project/Projects', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contract::orderBy('contract_id')->get();
        $authorizations = Authorization::orderBy('contract_id')->get();
        $quotes = Quote::get();
        $ships = Ship::with('customer', 'typeShip')->doesnthave('projectsShip')->get();
        $gerentes = getPersonalGerenciaOficina('GECON', 'DEGPC')->map(function ($estimador) {
            return [
                'user_id' => $estimador['Num_SAP'],
                'name' => $estimador['Nombres_Apellidos'],
                'email' => $estimador['Correo']
            ];
        })->toArray();
        // return $ships;
        return Inertia::render('Project/CreateProjects', compact('contracts', 'authorizations', 'quotes', 'ships', 'gerente'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validateData = $request->validate([
            'name' => 'required|unique:projects,name',
            'contract_id' => 'nullable',
            'authorization_id' => 'nullable',
            'quote_id' => 'nullable',
            'type' => 'nullable',
            'SAP_code' => 'nullable',
            'status' => 'nullable',
            'scope' => 'nullable',
            'supervisor' => 'nullable',
            'cost_sale' => 'nullable',
            'observations' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'hoursPerDay' => 'nullable',
            'daysPerWeek' => 'nullable',
            'daysPerMonth' => 'nullable',
            'shift' => 'nullable',
            // 'ships' => 'nullable|array'
        ]);

        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
            $id = Project::create($validateData)->id;
            foreach ($request->ships as $ship) {
                ProjectsShip::create([
                    'project_id' => $id,
                    'ship_id' => $ship,
                ]);
            }

            return response()->json([
                'project_id' => $id
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el proyecto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $taskProject = VirtualTask::where('project_id', $project->id)->whereNull('task_id')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'project_id' => $item->project->id,
                'avance' => number_format($item['percentDone'], 2),
                'name' => $item['name'],
                'file' => $item->project->contract->ship->file,
            ];
        });

        return response()->json($taskProject);
    }

    /**
     * Display the specified resource.
     */
    public function goToProjectOverview(Project $project)
    {
        try {
            $ships_ids = ProjectsShip::where('project_id', $project->id)->pluck('ship_id')->toArray();
            $ships = Ship::with('typeShip')->whereIn('id', $ships_ids)->get();
            $semana = ProgressProjectWeek::where('project_id', $project->id)->where('real_progress', '<>', 0)->orderBy('week', 'DESC')->first();

            return Inertia::render(
                'Project/ProjectOverview',
                [
                    'project' => Project::with('projectShip', 'contract', 'milestone')->findOrFail($project->id),
                    'ships' => $ships,
                    'semana' => $semana
                ]
            );
        } catch (Exception $e) {
            return back()->withErrors(['message', 'Error al cargar la página' . $e]);
        }
    }

    public function addShips(Request $request, Project $project)
    {
        foreach ($request->ships as $ship) {
            ProjectsShip::create([
                'project_id' => $project->id,
                'ship_id' => $ship
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $contracts = Contract::get();
        $authorizations = Authorization::orderBy('contract_id')->get();
        $quotes = Quote::get();
        $milestones = Milestone::where('project_id', $project->id)->get();
        $ships = Ship::with('customer', 'typeShip')->doesnthave('projectsShip')->get();
        $gerentes = getPersonalGerenciaOficina('GECON', 'DEGPC')->map(function ($estimador) {
            return [
                'user_id' => $estimador['Num_SAP'],
                'name' => $estimador['Nombres_Apellidos'],
                'email' => $estimador['Correo']
            ];
        })->toArray();
        // WeekTask::where('project_id', $project->id)->where();
        return Inertia::render(
            'Project/CreateProjects',
            [
                'project' => $project,
                'project_id' => $project->id,
                'contracts' => $contracts,
                'authorizations' => $authorizations,
                'quotes' => $quotes,
                'ships' => $ships,
                'milestones' => $milestones,
                'gerentes' => $gerentes
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'contract_id' => 'nullable',
            'authorization_id' => 'nullable',
            'quote_id' => 'nullable',
            'type' => 'nullable',
            'SAP_code' => 'nullable',
            'status' => 'nullable',
            'scope' => 'nullable',
            'supervisor' => 'nullable',
            'cost_sale' => 'nullable',
            'observations' => 'nullable',
            'gerencia' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'hoursPerDay' => 'nullable',
            'daysPerWeek' => 'nullable',
            'daysPerMonth' => 'nullable',
            'shift' => 'nullable',
            'file' => 'nullable',
            // 'ships' => 'nullable|array'
        ]);

        try {
            $project->update($validateData);
            foreach ($request->ships as $ship) {
                ProjectsShip::create([
                    'project_id' => $project->id,
                    'ship_id' => $ship,
                ]);
            }
        } catch (Exception $e) {
            dd($e);
            return back()->withErrors(['message', 'Ocurrió un Error Al Actualizar El Proyecto: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
