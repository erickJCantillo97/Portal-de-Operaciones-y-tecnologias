<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Personal\Team;
use App\Models\Personal\WorkingTeams;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
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
    {;
        $validateData = $request->validate([
            'name' => 'required|max:20',
            'description' => 'required',
        ]);

        $status = true;
        try {
            DB::beginTransaction();
            $validateData['user_id'] = auth()->user()->id;
            $validateData['gerencia'] = auth()->user()->gerencia;
            $team = Team::create($validateData);
            if ($team && $status) {
                foreach ($request->personal as $persona) {
                    if ($status) {
                        $workingTeams = WorkingTeams::create([
                            'user_num_sap' => $persona,
                            'team_id' => $team->id,
                            'user_id' => auth()->user()->id
                        ]);
                        $status = $workingTeams;
                    }
                }
            }
            $status = $team && $status;
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
        if ($status) {
            DB::commit();
            return back()->with('message', 'Grupo Creado');
        } else {
            DB::rollBack();
            return back()->withErrors('message', 'Ocurrio un Error Al Crear el Grupo');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $team->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        try {
            $team->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
