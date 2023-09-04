<?php

namespace App\Http\Controllers;

use App\Models\Gantt\Dependecy;
use Exception;
use Illuminate\Http\Request;

class DependecyController extends Controller
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
            Dependecy::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Dependecy $dependecy)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dependecy $dependecy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dependecy $dependecy)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $dependecy->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dependecy $dependecy)
    {
        try{
            $dependecy->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
