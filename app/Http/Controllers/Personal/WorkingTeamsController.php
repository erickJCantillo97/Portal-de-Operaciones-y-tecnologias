<?php

namespace App\Http\Controllers;

use App\Models\Personal\WorkingTeams;
use Exception;
use Illuminate\Http\Request;

class WorkingTeamsController extends Controller
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
            //
        ]);

        try{
            WorkingTeams::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkingTeams $workingTeams)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkingTeams $workingTeams)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkingTeams $workingTeams)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $workingTeams->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkingTeams $workingTeams)
    {
        try{
            $workingTeams->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
