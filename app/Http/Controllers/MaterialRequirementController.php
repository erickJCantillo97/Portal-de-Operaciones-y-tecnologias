<?php

namespace App\Http\Controllers;

use App\Models\WareHouse\Material;
use App\Models\WareHouse\MaterialRequirement;
use App\Models\WareHouse\Requirement;
use Exception;
use Illuminate\Http\Request;

class MaterialRequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Requirement $requirement)
    {
        $materials = MaterialRequirement::with('material', 'requirement')->where('requirement_id', $requirement->id)->orderBy('material_id')->get()->map(function ($m) {
            return [
                'id' => $m['id'],
                'material' => $m['material']['description'],
                'codigo' => $m['material']['code'],
                'cantidad' => $m['count'],
                'unidad' => array_search($m['unit'], Material::$unidad),
                'Estado' => array_search($m['status'], Material::$estado),
            ];
        });
        return response()->json([
            'material' => $materials,
        ], 200);
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
            //
        ]);

        try {
            MaterialRequirement::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialRequirement $materialRequirement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialRequirement $materialRequirement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialRequirement $materialRequirement)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $materialRequirement->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialRequirement $materialRequirement)
    {
        try {
            $materialRequirement->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
