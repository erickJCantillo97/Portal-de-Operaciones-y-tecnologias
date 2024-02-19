<?php

namespace App\Http\Controllers;

use App\Models\Project\WeekTask;
use Exception;
use Illuminate\Http\Request;

class WeekTaskController extends Controller
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
            'task' => 'required',
            'project_id' => 'required',
            'week' => 'required|string',
        ]);
        
        try{
            $year = explode('-', $validateData['week'])[0];
            $week_number = str_replace('W', '', explode('-', $validateData['week'])[1]);
            $validateData['week'] = substr($year, -2) . $week_number;
            WeekTask::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(WeekTask $weekTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WeekTask $weekTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WeekTask $weekTask)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $weekTask->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WeekTask $weekTask)
    {
        try{
            $weekTask->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
