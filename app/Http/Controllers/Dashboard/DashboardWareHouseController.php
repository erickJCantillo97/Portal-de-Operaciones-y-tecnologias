<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WareHouse\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardWareHouseController extends Controller
{
    public function getToolsStatus()
    {
        $warehouse = Warehouse::where('department', Auth::user()->oficina)->first()->id ?? 4;
        $statues =  Tool::where('warehouse_id', $warehouse)->where('estado_operativo', '<>', 'BAJA')->select(DB::raw('count(id) as value'), DB::raw('UPPER(estado_operativo) as status'))
            ->groupBy('estado_operativo')
            ->orderBy('estado_operativo')
            ->get();

        return response()->json([
            'statues' => $statues,
            'status' => true
        ], 200);
    }

    public function getAssigmentTool()
    {
        $warehouse = Warehouse::where('department', Auth::user()->oficina)->first()->id ?? 4;
        $tools =  Tool::where('warehouse_id', $warehouse)->where('estado_operativo', '<>', 'BAJA')->select(DB::raw('count(id) as value'), DB::raw('UPPER(estado) as status'))
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        return response()->json([
            'tools' => $tools,
            'status' => true
        ], 200);
    }
    public function getToolsCategories()
    {
        $warehouse = Warehouse::where('department', Auth::user()->oficina)->first()->id ?? 4;
        $tools =  Tool::where('warehouse_id', $warehouse)->select(
            DB::raw('count(id) as value'),
            DB::raw('UPPER(category_id) as category_id')
        )
            ->groupBy('category_id')
            ->orderBy('category_id')
            ->get();

        return response()->json([
            'tools' => $tools,
            'status' => true
        ], 200);
    }

    public function getTotalStatusCategories($category)
    {

        $warehouse = Warehouse::where('department', Auth::user()->oficina)->first()->id ?? 4;
        $tools = Tool::where('warehouse_id', $warehouse)->where('category_id', $category)
            ->select(DB::raw('count(id) as value'), DB::raw('UPPER(estado) as estado'))
            ->groupBy('estado')->get();

        return response()->json([
            'tools' => $tools,
            'status' => true
        ], 200);
    }
}
