<?php

namespace App\Http\Controllers;

use App\Models\Projects\Project;
use App\Models\WareHouse\Requirement;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequirementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::active()->get();
        return Inertia::render('WareHouse/Requirements/Index', [
            'projects' => $projects
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
            //
        ]);

        try {
            Requirement::create($validateData);
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
        //
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
}
