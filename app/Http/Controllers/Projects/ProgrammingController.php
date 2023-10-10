<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgrammingController extends Controller
{
    public function index()
    {
        return Inertia::render('Programming/Index');
    }

    public function store(Request $request){
        try{
            $validateData = $request->validate([
            'task_id' => 'required',
            'employee_id' => 'required',
            'fecha' => 'required|date',
        ]);
        $task = VirtualTask::find($validateData['task_id']);
        $validateData['hora_fin'] = '16:30';
        if($task->durationUnit == 'hours' && $task->duration < 8.5){
                $validateData['hora_fin'] = Carbon::parse('16:30')->addHours($task->duration)->format('H:i');
        }
        $validateData['hora_inicio'] = '7:00';
        $validateData['hora_fin'] = '16:30';
        Schedule::create($validateData);
        return response()->json([
            'status' => true,
        ], 200);
        }catch (Exception $e){
            return 500;
        }


    }

    public function indexGEMAM()
    {
        return Inertia::render('Programming/IndexGEMAM');
    }

    public function delete(Request $request){
        //hace algo
        return 'Hecho';
    }
}
