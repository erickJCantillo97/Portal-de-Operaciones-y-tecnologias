<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Authorization;
use App\Models\Projects\Contract;
use App\Models\Projects\Project;
use App\Models\Projects\Quote;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all('contract')->orderBy('name')->get();
        $contracts = Contract::get();

        return Inertia::render('Project/Projects', compact('projects, contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contract::orderBy('contract_id')->get();
        $authorizations = Authorization::orderBy('contract_id')->get();
        $quotes = Quote::orderBy('ship_id')->get();

        return Inertia::render('Project/CreateProjects', compact('contracts', 'authorizations', 'quotes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
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
            'description' => 'nullable',
            'gerencia' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'hoursPerDay' => 'nullable',
            'daysPerWeek' => 'nullable',
            'daysPerMonth' => 'nullable',
            'shift' => 'nullable'
        ]);

        try {
            if (($validateData['contract_id']) != 0) {
                $buque = Contract::find($validateData['contract_id'])->ship;
            }

            if ($validateData['authorization_id'] != 0) {
                $buque = Authorization::find($validateData['authorization_id'])->first()->quote->ship;
            }

            if ($validateData['quote_id'] != 0) {
                $buque = Quote::find($validateData['quote_id'])->ship;
            }

            if (isset($buque) && $validateData['ship_id'] != 0) {
                $buque = Ship::find($validateData['ship_id']);
            }

            if (isset($buque) && $validateData['intern_communications'] != 0) {
                $buque = Ship::find($validateData['ship_id']);
            }

            if (!isset($buque)) {
                return back()->withErrors(['message' => 'Ocurrió un error al crear el proyecto: No se seleccionó un Buque'], 500);
            }

            // Verificar si ya existe un proyecto con el mismo contract_id y ship_id
            $existingProjectWithContract = Project::where('contract_id', $validateData['contract_id'])
                ->where('ship_id', $buque->id)
                ->first();

            if ($existingProjectWithContract) {
                return back()->withErrors(['message' => 'Ya existe un proyecto con el mismo contrato y buque'], 500);
            }

            // Verificar si ya existe un proyecto con el mismo authorization_id y ship_id
            $existingProjectWithAuthorization = Project::where('authorization_id', $validateData['authorization_id'])
                ->where('quote_id', $buque->id)
                ->first();

            if ($existingProjectWithAuthorization) {
                return back()->withErrors(['message' => 'Ya existe un proyecto con la misma autorización y buque'], 500);
            }

            // Verificar si ya existe un proyecto con la misma Estimación
            $existingProjectWithQuote = Project::where('quote_id', $validateData['quote_id'])
                ->where('quote_id', $buque->id)
                ->first();

            if ($existingProjectWithQuote) {
                return back()->withErrors(['message' => 'Ya existe un proyecto con la misma estimación y buque'], 500);
            }

            $countProject = count(Project::where('ship_id', $buque->id)->get()) + 1;

            $validateData['name'] = $buque->name . '-' . Carbon::now()->format('Y') . '-' . str_pad($countProject, 3, '0', STR_PAD_LEFT);
            $validateData['gerencia'] = auth()->user()->gerencia;

            //Guardar archivo pdf de Comunicación Interna
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Interns_Communications/',
                    $request->pdf,
                    $validateData['name'] . '.' . $request->pdf->getClientOriginalExtension()
                );
            }

            Project::create($validateData);

            return back()->with(['message' => 'Proyecto creado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el proyecto: ' . $e->getMessage()], 500);
        }

        return redirect('projects.index');
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
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $contracts = Contract::orderBy('name')->get();
        $authorizations = Authorization::orderBy('contract_id')->get();
        $quotes = Quote::orderBy('ship_id')->get();

        return Inertia::render(
            'Project/CreateProjects',
            [
                'project' => $project,
                'contracts' => $contracts,
                'authorizations' => $authorizations,
                'quotes' => $quotes,
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
            'description' => 'nullable',
            'gerencia' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'hoursPerDay' => 'nullable',
            'daysPerWeek' => 'nullable',
            'daysPerMonth' => 'nullable',
            'shift' => 'nullable',
            'file' => 'nullable',
        ]);

        try {
            $project->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrió un Error Al Actualizar : ' . $e);
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
