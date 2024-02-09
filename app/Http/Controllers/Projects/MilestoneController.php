<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Milestone;
use Exception;
use Illuminate\Http\Request;

class MilestoneController extends Controller
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
            'title' => 'required',
            'project_id' => 'required',
            'value' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'invoiced' => 'nullable',
            'advance' => 'nullable|numeric',
        ]);

        try {
            Milestone::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Milestone $milestone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Milestone $milestone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Milestone $milestone)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $milestone->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Milestone $milestone)
    {
        try {
            $milestone->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
