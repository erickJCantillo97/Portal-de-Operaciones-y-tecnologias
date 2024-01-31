<?php

namespace App\Http\Controllers\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\WareHouse\AssignmentTool;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AssignmentToolNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class AssignmentToolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignmentsTool = AssignmentTool::get();
        $projects = Project::orderBy('created_at', 'DESC')->get();
        return Inertia::render('WareHouse/Assignment', compact('assignmentsTool', 'projects'));
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
            'employee_id' => 'required',
            'supervisor_id' => 'required',
            'project_id' => 'required',
            'tools' => 'required|array',
            'email' => 'required|email'
        ]);
        //correo user, username, correo persona, nombre persona
        try {
            $validateData['user_id'] = auth()->user()->id;
            $validateData['gerencia'] = auth()->user()->gerencia;

            AssignmentTool::create($validateData);

            Notification::route('mail', [ auth()->user()->username.'@cotecmar.com' => auth()->user()->short_name, $request->email => $request->employee_name])
            ->notify(new AssignmentToolNotification(auth()->user(), $request->employee_name, $request->tools,
            'Le han asignado Equipo(s)'));
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentTool $assignmentTool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignmentTool $assignmentTool)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignmentTool $assignmentTool)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $assignmentTool->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentTool $assignmentTool)
    {
        try {
            $assignmentTool->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
