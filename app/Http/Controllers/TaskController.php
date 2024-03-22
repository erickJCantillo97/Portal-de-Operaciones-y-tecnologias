<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Ldap\User;
use App\Models\Gantt\Task;
use App\Models\Schedule;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = VirtualTask::where('project_id', $request->id)->get();

        return response()->json([
            'tasks' => $tasks,
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

        try {
            Task::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, VirtualTask $task)
    {
        $division = $request->division;
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $schedules = Schedule::with('employee')->where('fecha', $date)->where('task_id', $task->id)->get();

        return response()->json([
            'schedules' => $schedules,
        ]);
        // $p = Schedule::where();
    }
    public function getScheduleTaskDate(Request $request)
    {
        $division = $request->division;
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $schedules = Schedule::where('fecha', $date)->where('task_id', $request->task)->get()->map(
            function ($e) {
                return [
                    'name' => $e['name'],
                    'photo' =>  User::where('userprincipalname', $e['employee']['Correo'])->first()->photo(),
                ];
            }
        );
        return response()->json([
            'schedules' => $schedules,
        ]);
        // $p = Schedule::where();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $task->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }

    public function excelImport(Request $request)
    {

        Excel::import(new UsersImport($request->project_id), $request->file);

        return back()->with('message', 'Archivo cargado correctamente');
    }
}
