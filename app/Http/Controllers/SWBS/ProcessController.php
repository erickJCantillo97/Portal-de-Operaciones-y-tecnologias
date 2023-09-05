<?php

namespace App\Http\Controllers\SWBS;

use App\Http\Controllers\Controller;
use App\Models\Process;
use Exception;
use App\Http\Requests\StoreprocessRequest;
use App\Http\Requests\UpdateprocessRequest;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $process = Process::orderBy('id')->get();

        return response()->json([
                $process
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
    public function store(StoreprocessRequest $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            process::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(process $process)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(process $process)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateprocessRequest $request, process $process)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $process->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(process $process)
    {
        try{
            $process->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
