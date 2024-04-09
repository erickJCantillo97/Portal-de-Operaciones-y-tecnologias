<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Personal\ContractorEmployee;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContractorEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = ContractorEmployee::get();

        return Inertia::render('Personal/Contractor/Index', compact('employees'));
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
            'contractor' => 'required',
            'name' => 'required',
            'labor' => 'nullable',
        ]);

        try {
            ContractorEmployee::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContractorEmployee $contractorEmployee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContractorEmployee $contractorEmployee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContractorEmployee $contractorEmployee)
    {
        $validateData = $request->validate([
            'contractor' => 'required',
            'name' => 'required',
            'labor' => 'nullable',
        ]);

        try {
            $contractorEmployee->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContractorEmployee $contractorEmployee)
    {
        try {
            $contractorEmployee->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
