<?php

namespace App\Http\Controllers;

use App\Models\Personal\Employee;
use App\Models\Personal\ProgrammingAdvance;
use App\Models\ScheduleTime;
use App\Models\Views\DetailScheduleTime;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ProgrammingAdvanceController extends Controller
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

        // $table->integer('user_id');
        // $table->integer('schedule_id');
        // $table->integer('task_id');
        // $table->double('progress');
        // $table->dateTime('start');
        // $table->dateTime('end');
        // $table->double('cost');
        // $table->date('date');
        $validateData = $request->validate([
            'schedule_id' => 'required',
            'progress' => 'required',
            'start' => 'required',
            'end' => 'required',
        ]);

        $horas = Carbon::parse($validateData['start'])->diffInRealHours(Carbon::parse($validateData['end']));

        $validateData['date'] = Carbon::now();
        $validateData['user_id'] = auth()->user()->id;
        $validateData['task_id'] = DetailScheduleTime::where('idScheduleTime', $validateData['schedule_id'])->idTask;
        $validateData['cost'] = Employee::where('Num_SAP', 'LIKE', '%' . auth()->user()->num_sap)->Costo_Hora * ($horas > 9.5 ? $horas - 1 : $horas);
        try {
            ProgrammingAdvance::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgrammingAdvance $programmingAdvance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgrammingAdvance $programmingAdvance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgrammingAdvance $programmingAdvance)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $programmingAdvance->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgrammingAdvance $programmingAdvance)
    {
        try {
            $programmingAdvance->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
