<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Jobs\ExcelImport;
use App\Models\Gantt\Task;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
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

        try {
            Task::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
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
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
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
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }

    public function excelImport(Request $request)
    {

        Excel::import(new UsersImport($request->project_id), $request->file);

        return back()->with('message', 'Archivo cargado correctamente');

    }
}
