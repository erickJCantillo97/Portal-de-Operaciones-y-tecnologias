<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::orderBy('name')->get();

        return Inertia::render(
            'Project/Projects',
            [
                'projects' => $projects,
                'gerencias' => gerencias(),
            ]
        );

        // return response()->json([
        //     $project
        // ], 200);
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
        // dd($request);
        $validateData = $request->validate([
            'ship_id' => 'nullable',
            'name' => 'required',
            'gerencia' => 'required',
            'start_date' => 'required|date',
            'hoursPerDay' => 'required',
            'daysPerWeek' => 'required',
            'daysPerMonth' => 'required',
        ]);

        try {
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'gerencia' => 'required',
            'cost' => 'required',
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
