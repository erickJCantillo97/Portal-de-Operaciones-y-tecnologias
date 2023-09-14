<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Authorization;
use App\Models\Projects\Contract;
use App\Models\Projects\Customer;
use App\Models\Projects\Project;
use App\Models\Projects\Quote;
use App\Models\Projects\Ship;
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
        $projects = Project::with('contract', 'ship', 'customer')->orderBy('name')->get();
        $ships = Ship::orderBy('name')->get();
        $contracts = Contract::orderBy('name')->get();

        return Inertia::render('Project/Projects',
            [
                'projects' => $projects,
                'ships' => $ships,
                'contracts' => $contracts,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contract::has('ship')->orderBy('name')->get();
        $authorizations = Authorization::orderBy('contract_id')->get();
        $quotes = Quote::orderBy('ship_id')->get();
        $ships = Ship::orderBy('id')->get();
        $customers = Customer::orderBy('name')->get();

        return Inertia::render('Project/CreateProjects',
            [
                'contracts' => $contracts,
                'authorizations' => $authorizations,
                'quotes' => $quotes,
                'ships' => $ships,
                'customers' => $customers,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validateData = $request->validate([
            'contract_id' => 'nullable',
            'authorization_id' => 'nullable',
            'quote_id' => 'nullable',
            'ship_id' => 'nullable',
            'customer_id' => 'nullable',
            'intern_communications' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'hoursPerDay' => 'required',
            'daysPerWeek' => 'required',
            'daysPerMonth' => 'required',
            'file' => 'nullable',
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

            if (! isset($buque)) {
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

            // Verificar si ya existe un proyecto con la misma Estimación
            $existingProjectWithInternCommunications = Project::where('intern_communications', $validateData['intern_communications'])
                ->where('intern_communications', $buque->id)
                ->first();

            if ($existingProjectWithInternCommunications) {
                return back()->withErrors(['message' => 'Ya existe un proyecto asociado con este consecutivo.'], 500);
            }

            $countProject = count(Project::where('ship_id', $buque->id)->get()) + 1;

            $validateData['ship_id'] = $buque->id;
            $validateData['name'] = $buque->name.'-'.Carbon::now()->format('Y').'-'.str_pad($countProject, 3, '0', STR_PAD_LEFT);
            $validateData['gerencia'] = auth()->user()->gerencia;

            //Guardar archivo pdf de Comunicación Interna
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Interns_Communications/',
                    $request->pdf,
                    $validateData['name'].'.'.$request->pdf->getClientOriginalExtension()
                );
            }

            Project::create($validateData);

            return back()->with(['message' => 'Proyecto creado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el proyecto: '.$e->getMessage()], 500);
        }

        return redirect('projects.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $projects = Project::orderBy('name')->get();
        $contracts = Contract::orderBy('name')->get();
        $authorizations = Authorization::orderBy('contract_id')->get();
        $quotes = Quote::orderBy('ship_id')->get();
        $ships = Ship::orderBy('id')->get();

        return Inertia::render('Project/CreateProjects',
            [
                'projects' => $projects,
                'contracts' => $contracts,
                'authorizations' => $authorizations,
                'quotes' => $quotes,
                'ships' => $ships,
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
            'gerencia' => 'required',
            'cost' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        try {
            $project->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
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
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
