<?php

namespace App\Http\Controllers\SWBS;

use App\Http\Controllers\Controller;
use App\Models\SWBS\System;
use Exception;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $systems = System::get();
        if($request->grupo){
            $systems = System::where('constructive_group_id', 'LIKE', ($request->grupo/100))->orderBy('code')->get();
        }

        return response()->json([
            $systems
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
            System::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(System $system)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(System $system)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, System $system)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        try{
            $system->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(System $system)
    {
        try{
            $system->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
