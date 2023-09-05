<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Authorization;
use App\Models\Projects\Contract;
use App\Models\Projects\Project;
use App\Models\Projects\Quote;
use App\Models\Projects\Ship;
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
        $projects = Project::orderBy('name')->get();
        $ships = Ship::orderBy('name')->get();
        $contracts = Contract::orderBy('name')->get();

        return Inertia::render('Project/Projects',
            [
                'projects' => $projects,
                'ships' => $ships,
                'contracts' => $contracts,
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
            'contract_id' => 'nullable',
            'authorization_id' => 'nullable',
            'quote_id' => 'nullable',
            'ship_id' => 'nullable',
            'intern_communications' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'hoursPerDay' => 'required',
            'daysPerWeek' => 'required',
            'daysPerMonth' => 'required',
        ]);

        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
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

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048',
        ]);

        if ($request->hasFile('file')) {
            // Obtener el archivo del formulario
            $file = $request->file('file');

            // Almacena el archivo utilizando el disco "public" (con la configuración en filesystems.php)
            $path = Storage::disk('public')->putFile('uploads', $file);

            // Puedes guardar la ruta en la base de datos si es necesario
            File::create(['file_path' => $path]);

            return redirect()->back()->with('success', 'Archivo cargado correctamente.');
        }

        return redirect()->back()->with('error', 'No se ha seleccionado ningún archivo.');
    }
}
