<?php

namespace App\Http\Controllers;

use App\Imports\Projects\ProgressImport;
use App\Models\Project\ProgressProjectWeek;
use App\Models\Projects\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Prompts\Progress;
use Maatwebsite\Excel\Facades\Excel;

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

    public function getDataProject(Request $request)
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

    public function getDataWeek(Request $request)
    {
        $projects = Project::active();
        $projects_id = $projects->pluck('id')->toArray();
        $semanas = ProgressProjectWeek::where('real_progress', '<>', 0)->whereIn('project_id', $projects_id)->groupBy('project_id')->select('project_id', DB::raw("MAX(week) as week"))->get();
        $marks = [];
        $progress = [];
        foreach ($semanas as $semana) {
            $progre =  ProgressProjectWeek::where('project_id', $semana->project_id)->where('week', $semana->week)->first();
            array_push($marks, $progre);
        }

        return response()->json([
            'indicators' => collect($marks)->map(function ($p) {
                return [
                    'project' => Project::find($p->project_id)->name,
                    'indicators' => ['CPI' => $p->CPI, 'SPI' =>  $p->SPI],
                    'real_progress' => $p->real_progress,
                    'planned_progress' => $p->planned_progress
                ];
            }),

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

    public function upload(Request $request, $project)
    {
        try {
            $prgress = ProgressProjectWeek::where('project_id', $project)->delete();
            // dd($request->files->get('files'));
            Excel::import(new ProgressImport($project), $request->docs);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
    }
}
