<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function get()
    {

        return response()->json([
            "success"=> true,
            "proyect"=>['rows' => ['Datos']],
            "tasks" => ['rows' => ['Datos']],
            "dependencies" => ['rows' => ['Datos']],
            "resources" => ['rows' => ['Datos']],
            "assignments" => ['rows' => ['Datos']],
            "timeRanges" => ['rows' => ['Datos']]
        ]);
    }

    public function sync(Request $request)
    {

        // dd($request->requestId);

        return response()->json(['requestId' => $request->requestId]);
    }
}
