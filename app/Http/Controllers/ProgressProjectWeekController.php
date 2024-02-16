<?php

namespace App\Http\Controllers;

use App\Models\Project\ProgressProjectWeek;
use Exception;
use Illuminate\Http\Request;
use Laravel\Prompts\Progress;

class ProgressProjectWeekController extends Controller
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
            'project_id' => 'required',
            'week' => 'required',
            'real_progress' => 'required',
            'CPI' => 'required|numeric',
            'SPI' => 'required|numeric',
        ]);

        try {
            $year = explode('-', $validateData['week'])[0];
            $week_number = str_replace('W', '', explode('-', $validateData['week'])[1]);
            $validateData['week'] = substr($year, -2) . $week_number;

            ProgressProjectWeek::where('project_id', $validateData['project_id'])->where('week', $validateData['week'])->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgressProjectWeek $progressProjectWeek)
    {
    }

    public function getData(Request $request)
    {
        $semanas = ProgressProjectWeek::where('project_id', $request->project)->pluck('week')->toArray();
        $planeado = ProgressProjectWeek::where('project_id', $request->project)->pluck('planned_progress')->toArray();

        $ejecutado = ProgressProjectWeek::where('project_id', $request->project)->pluck('real_progress')->toArray();
        return response()->json([
            'labels' => $semanas,
            'planeado' => $planeado,
            'ejecutado' => $ejecutado,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgressProjectWeek $progressProjectWeek)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgressProjectWeek $progressProjectWeek)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $progressProjectWeek->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgressProjectWeek $progressProjectWeek)
    {
        try {
            $progressProjectWeek->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
