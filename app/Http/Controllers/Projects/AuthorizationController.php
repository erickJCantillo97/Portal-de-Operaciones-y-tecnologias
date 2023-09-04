<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Authorization;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthorizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authorizations = Authorization::orderBy('project_id')->get();

        return Inertia::render(
            'Project/Authorizations',
            [
                'authorizations' => $authorizations,
                'gerencias' => gerencias(),
            ]
        );
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
            'project_id' => 'nullable',
            'contract_id' => 'nullable',
            'gerencia' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            Authorization::create($validateData);

            return back()->with(['message' => 'Autorización creado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear la Autorizacion: '.$e->getMessage()], 500);
        }

        return redirect('authorizations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Authorization $authorization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Authorization $authorization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Authorization $authorization)
    {
        $validateData = $request->validate([
            'project_id' => 'nullable',
            'contract_id' => 'nullable',
            'gerencia' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            $authorization->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Authorization $authorization)
    {
        try {
            $authorization->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
