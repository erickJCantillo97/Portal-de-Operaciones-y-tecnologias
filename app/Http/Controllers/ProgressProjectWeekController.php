<?php

namespace App\Http\Controllers;

use App\Models\Project\ProgressProjectWeek;
use Exception;
use Illuminate\Http\Request;

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
            'CPI' => 'required',
            'SPI' => 'required',
        ]);

        try {
            $year = explode('-', $validateData['week'])[0];
            $week_number = str_replace('W', '', explode('-', $validateData['week'])[1]);
            $validateData['week'] = substr($year, -2) . $week_number;
            ProgressProjectWeek::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgressProjectWeek $progressProjectWeek)
    {
        //
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
