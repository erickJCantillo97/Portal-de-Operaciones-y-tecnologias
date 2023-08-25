<?php

namespace App\Http\Controllers\SWBS;

use App\Http\Controllers\Controller;
use App\Models\SWBS\SpecificActivity;
use Exception;
use Illuminate\Http\Request;

class SpecificActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specificActivity = SpecificActivity::orderBy('name')->get();

        return response()->json([
                $specificActivity
            ], 200);
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
            'name' => 'required|unique:specific_activities,name'
        ]);

        try{
            SpecificActivity::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SpecificActivity $specificActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SpecificActivity $specificActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SpecificActivity $specificActivity)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $specificActivity->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SpecificActivity $specificActivity)
    {
        try{
            $specificActivity->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
