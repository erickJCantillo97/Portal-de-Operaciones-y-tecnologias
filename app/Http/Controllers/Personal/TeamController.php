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

        // $status = true;
        try {
            DB::beginTransaction();
            $validateData['user_id'] = auth()->user()->id;
            $validateData['gerencia'] = auth()->user()->gerencia;
            Team::create($validateData);
            // if ($team && $status) {
            //     foreach ($request->personal as $persona) {
            //         if ($status) {
            //             $workingTeams = WorkingTeams::create([
            //                 'user_num_sap' => $persona['Num_SAP'],
            //                 'team_id' => $team->id,
            //                 'user_id' => auth()->user()->id
            //             ]);
            //             $status = $workingTeams;
            //         }
            //     }
            // }
            // $status = $team && $status;
            // if ($status) {
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Grupo creado']);
            // }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => 'Error no controlado ', 'error' => $e]);
            // return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
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
            'name' => 'required|max:20',
            'description' => 'required',
        ]);
        try {
            DB::beginTransaction();
            $team->update($validateData);
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Grupo actualizado']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'mensaje' => 'Error no controlado ', 'error' => $e]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        try {
            DB::beginTransaction();
            $team->delete();
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Grupo eliminado']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'mensaje' => 'Error no controlado ', 'error' => $e]);
        }
    }

    public function addPersonTeam(Team $team, Request $request)
    {
        $person = $request->validate([
            'Num_SAP' => 'required',
        ]);
        try {
            DB::beginTransaction();
            WorkingTeams::firstOrCreate([
                'user_num_sap' => $person['Num_SAP'],
                'team_id' => $team->id,
                'user_id' => auth()->user()->id
            ]);
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Persona agregada al grupo ' . $team->name]);
            // }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => 'Error no controlado ', 'error' => $e]);
            // return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }
    public function removePersonTeam(Team $team, Request $request)
    {
        $person = $request->validate([
            'Num_SAP' => 'required',
        ]);
        try {
            DB::beginTransaction();

            WorkingTeams::where([
                'user_num_sap' => $person['Num_SAP'],
                'team_id' => $team->id,
            ])->delete();
            DB::commit();
            return response()->json(['status' => true, 'mensaje' => 'Persona eliminada del grupo ' . $team->name]);
            // }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'mensaje' => 'Error no controlado ', 'error' => $e]);
            // return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }
}
