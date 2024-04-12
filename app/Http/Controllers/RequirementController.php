<?php

namespace App\Http\Controllers;

use App\Imports\RequirementImport;
use App\Models\Projects\Project;
use App\Models\WareHouse\MaterialRequirement;
use App\Models\WareHouse\Requirement;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::active()->get();
        $requirements = Requirement::has('project')->with('project', 'user')->get()->map(function ($requirement) {
            return [
                'id' => $requirement->id,
                'consecutivo' => $requirement->consecutive,
                'proyecto' => $requirement->project->name,
                'bloque' => $requirement->bloque,
                'grupo' => $requirement->sistema_grupo,
                'dibujante' => $requirement->user->short_name,
                'fecha' => Carbon::parse($requirement->preeliminar_date)->format('d-m-Y'),
            ];
        });

        return Inertia::render('WareHouse/Requirements/Index', [
            'projects' => $projects,
            'requirements' => $requirements,

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'project_id' => 'required',
            'document' => 'nullable',
            'bloque' => 'required|numeric',
            'sistema_grupo' => 'nullable',
            'note' => 'nullable',
        ]);
        try {
            Excel::import(new RequirementImport($validateData), $request->data);
            // Requirement::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Requirement $requirement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Requirement $requirement)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Requirement $requirement)
    {

        $validateData = $request->validate([
            //
        ]);

        try {
            $requirement->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Requirement $requirement)
    {
        try {
            $requirement->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }

    public function manageRequirements(Request $request)
    {
        // dd($request->all());
        $materials = MaterialRequirement::with('material', 'requirement')->whereIn('requirement_id', $request->requirements)->orderBy('material_id')->get();

        return Inertia::render('WareHouse/Requirements/Form', [
            'materials' => $materials,
        ]);
    }

    public function storeRequirementOficials(Request $request)
    {
        foreach ($request->all() as $requirement) {
            return $requirement[0]['nivel'];
        }

        // return $request->all();
    }
}
