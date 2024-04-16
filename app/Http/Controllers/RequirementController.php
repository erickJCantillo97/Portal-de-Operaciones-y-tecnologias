<?php

namespace App\Http\Controllers;

use App\Imports\RequirementImport;
use App\Models\Projects\Project;
use App\Models\WareHouse\Material;
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

    private function getAttributesRequiremnts($requirement)
    {

        return [
            'id' => $requirement->id,
            'consecutivo' => $requirement->consecutive,
            'proyecto' => $requirement->project->name,
            'bloque' => $requirement->bloque,
            'grupo' => $requirement->sistema_grupo,
            'dibujante' => $requirement->user->short_name,
            'estado' => $requirement->estado,
            'fecha' => Carbon::parse($requirement->preeliminar_date)->format('d-m-Y'),
        ];
    }
    public function index(Request $request)
    {
        //  dd($request->all());
        $projects = Project::active()->get();
        $requirements = [];
        if (auth()->user()->hasRole('Super intendente de Materiales' . '%TOP%' . auth()->user()->gerencia)) {
            $requirements = Requirement::has('project')->with('project', 'user')->get()->map(function ($requirement) {
                return $this->getAttributesRequiremnts($requirement);
            });
        } else if (auth()->user()->hasRole('ADMIN DIPR' . '%TOP%' . auth()->user()->gerencia)) {
            $requirements = Requirement::has('project')->with('project', 'user')->whereNull('oficial_date')->get()->map(function ($requirement) {
                return $this->getAttributesRequiremnts($requirement);
            });
        }

        return Inertia::render('WareHouse/Requirements/Index', [
            'projects' => $projects,
            'requirements' => $requirements,
            'requirement_id' => $request->requirement
        ]);
    }

    public function getRequirementByRole()
    {
        $requirements = [];
        if (auth()->user()->hasRole('ADMIN' . '%TOP%' . auth()->user()->gerencia)) {
            // return Requirement::has('project')->with('project', 'user')->get();
            $requirements = Requirement::has('project')->with('project', 'user')->get()->map(function ($r) {
                return [
                    'id' => $r->id,
                    'title' => 'Requerimiento ' . $r->consecutive,
                    'user' => $r->user->short_name,
                    'message' => 'Requerimiento en espera de Aprobación',
                    'ago' => Carbon::parse($r->created_at)->diffForHumans(),
                ];
            });
        }
        return response()->json([
            'requirements' => $requirements
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
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
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
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
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
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
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
        // return $request->materiales[0];
        $validateData = $request->validate([
            'materiales' => 'array|required',
            'materiales.*.nivel' => 'required',
            'materiales.*' => 'required|array',
            'materiales.*.unidad' => 'required',
            'materiales.*.cantidad' => 'required|numeric',
            'materiales.*.estado' => 'required|numeric',
            'materiales.*.codigo_material' => 'required|string',
            'materiales.*.observacion' => 'nullable|string',
        ]);

        foreach ($request['materiales'] as $material) {
            // return $material['codigo_material'];
            if ($material['nivel'] == 1) {
                MaterialRequirement::find($material['material']['id'])->update(
                    [
                        'count' => $material['cantidad'],
                        'status' => $material['estado'],
                        'unit' => $material['unidad'],
                        'observation' => $material['observacion'],
                    ]
                );

                // dd($material['material']['material']['id']);
                Material::find($material['material']['material']['id'])->update([
                    'code' => $material['codigo_material'],
                ]);
            } else {
            }
        }
        Requirement::find($request['materiales'][0]['material']['requirement_id'])->update([
            'intendente_id' => auth()->user()->id,
            'oficial_date' => Carbon::now(),
        ]);
        return back();
        // return $request->all();
    }
}
