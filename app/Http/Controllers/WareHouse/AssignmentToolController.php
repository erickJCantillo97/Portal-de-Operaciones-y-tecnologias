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
use App\Models\Warehouse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;
use Illuminate\Support\Facades\DB;

class AssignmentToolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignmentsTool = AssignmentTool::orderBy('assigment_date', 'DESC')->has('tool')->where('status', 'ASIGNADO')->with('tool', 'project')->get();
        $projects = Project::orderBy('created_at', 'DESC')->get();
        $warehouse = Warehouse::where('department', auth()->user()->oficina)->first()->id ?? 4;
        $tools = Tool::where('warehouse_id', $warehouse)->with('category', 'warehouse')->orderBy('category_id')->where('estado', '!=', 'ASIGNADO')->get();


        return Inertia::render('WareHouse/Assignment', compact('assignmentsTool', 'projects', 'tools'));
    }

    public function getDataAnterior()
    {
        AssignmentTool::truncate();
        $equipos = DB::connection('sqlsrv_anterior')->table('asignacions')->get();
        foreach ($equipos as $e) {
            $empleado = collect(searchEmpleados('Num_SAP', $e->persona_id))->first();
            $equipo =  DB::connection('sqlsrv_anterior')->table('equipos')->where('id', $e->equipo_id)->first();
            if ($equipo && $e->estado == 'ASIGNADO') {
                $tool = Tool::where('code', $equipo->codigo_interno)->first();
                if ($tool) {
                    AssignmentTool::firstorCreate([
                        'tool_id' => $tool->id,
                        'employee_id' => $e->persona_id,
                        'project_id' => $e->proyecto_id,
                        'supervisor_id' => $e->supervisor_id,
                        'user_deliver' => $e->almacenista_entrega,
                        'location' => 0,
                        'assigment_date' => $e->fecha_prestamo,
                        'status' => $e->estado,
                        'employee_name' => $empleado['Nombres_Apellidos'],
                        'gerencia' => 'GECON'
                    ]);
                }
            }
        }
        // Category::where('level', 'Sub Grupo')->update(['level' => 'Subgrupo']);
        return AssignmentTool::get();
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
            'project_id' => 'nullable',
            'tools' => 'nullable|array',
            'email' => 'required|email'
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
                    'project_id' => $validateData['project_id'] ?? 0,
                    'supervisor_id' => $validateData['supervisor_id'],
                    'user_deliver' => $validateData['user_id'],
                    'location' => '1',
                    'assigment_date' => Carbon::now(),
                    'gerencia' => $validateData['gerencia'],
                ]);

                Tool::find($tool)->update(['estado' => 'ASIGNADO']);
            }

            Mail::to([$request->email])->send(
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
            'status' => 'required',
            'observation' => 'nullable|string'
        ]);

        try {
            $assignmentTool->update([
                'status' => 'DISPONIBLE',
                'observation' => $validateData['observation'] ?? ''
            ]);
            $assignmentTool->tool->update([
                'estado_operativo' => strtoupper($validateData['status']),
                'estado' => 'DISPONIBLE'
            ]);
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
