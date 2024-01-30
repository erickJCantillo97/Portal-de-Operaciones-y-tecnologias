<?php

namespace App\Http\Controllers\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\WareHouse\Tool;
use Exception;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'category_id' => 'required',
            'serial' => 'required|unique:tools,serial',
            'SAP_code' => 'nullable|string',
            'value' => 'required',
            'brand_id' => 'nullable',
            'entry_date' => 'required|date',
            'is_small' => 'nullable',
            'description' => 'nullable|string',
            'imagen' => 'nullable'
        ]);

        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
            $validateData['responsible_id'] = auth()->user()->gerencia;

            Tool::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Tool $tool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tool $tool)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $tool->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tool)
    {
        try {
            $tool->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
