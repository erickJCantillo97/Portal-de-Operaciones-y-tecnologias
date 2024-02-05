<?php

namespace App\Http\Controllers\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\Projects\Project;
use App\Models\WareHouse\AssignmentTool;
use App\Models\WareHouse\Tool;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AssignmentToolNotification;
use Carbon\Carbon;
use App\Mail\AssignmentToolsMail;
use Illuminate\Support\Facades\Mail;
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
        $assignmentsTool = AssignmentTool::with('tool')->get();
        $projects = Project::orderBy('created_at', 'DESC')->get();
        $tools = Tool::where('estado', '!=', 'ASIGNADO')->with('category')->get();

        return Inertia::render('WareHouse/Assignment', compact('assignmentsTool', 'projects', 'tools'));
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
            'tools' => 'nullable|array',
            'email' => 'required|email'
        ], [
            'project_id' => 'Es requerido',
        ]);
        //correo user, username, correo persona, nombre persona
        try {
            $validateData['user_id'] = auth()->user()->id;
            $validateData['gerencia'] = auth()->user()->gerencia;
            foreach ($validateData['tools'] as $tool) {
                AssignmentTool::create([
                    'tool_id' =>  $tool,
                    'employee_name' => $request->employee_name,
                    'employee_id' => $validateData['employee_id'],
                    'project_id' => $validateData['project_id'],
                    'supervisor_id' => $validateData['supervisor_id'],
                    'user_deliver' => $validateData['user_id'],
                    'location' => '1',
                    'assigment_date' => Carbon::now(),
                    'gerencia' => $validateData['gerencia'],
                ]);

                Tool::find($tool)->update(['estado' => 'ASIGNADO']);
            }

            Mail::to([auth()->user()->username . '@cotecmar.com', 'rgutierrez@cotecmar.com'])->send(
                new AssignmentToolsMail($request->employee_name, $request->tools)
            );


        } catch (Exception $e) {
            dd($e);
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
