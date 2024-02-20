<?php

namespace App\Http\Controllers;

use App\Models\Projects\ProjectsShip;
use Exception;
use Illuminate\Http\Request;

class ProjectsShipController extends Controller
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
            ProjectsShip::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectsShip $projectsShip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectsShip $projectsShip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectsShip $projectsShip)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $projectsShip->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectsShip $projectship)
    {
        try{
            $projectship->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
