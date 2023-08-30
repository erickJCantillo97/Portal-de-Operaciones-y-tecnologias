<?php

namespace App\Http\Controllers;

use App\Models\SWBS\SubSystem;
use Exception;
use Illuminate\Http\Request;

class SubSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subsystems = SubSystem::get();

        return response()->json([
            $subsystems
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

        try{
            SubSystem::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubSystem $subSystem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubSystem $subSystem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubSystem $subSystem)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $subSystem->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubSystem $subSystem)
    {
        try{
            $subSystem->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
