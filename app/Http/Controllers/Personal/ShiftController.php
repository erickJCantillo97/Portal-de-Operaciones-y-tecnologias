<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(String $user = ""): JsonResponse
    {
        if(empty($user))
                return response()->json([
                    Shift::whereNull('user')->orderBy('name')->get()->map(function ($shift) {
                        return [
                            'id' => $shift['id'],
                            'name' => $shift['name'],
                            'startShift' => $shift['startShift'],
                            'endShift' => $shift['endShift'],
                            'timeBreak'=>$shift['timeBreak'],
                            'hours' => $shift['hours'],
                            'status' => $shift['status'],
                            'description' => $shift['description']
                        ];
                    })
                ], 200);

        return response()->json([
            Shift::whereNotNull('user')->orderBy('name')->get()->map(function ($shift) {
                return [
                    'id' => $shift['id'],
                    'name' => $shift['name'],
                    'startShift' => $shift['startShift'],
                    'endShift' => $shift['endShift'],
                    'timeBreak'=>$shift['timeBreak'],
                    'hours' => $shift['hours'],
                    'status' => $shift['status'],
                    'description' => $shift['description']
                ];
            })
                ], 200);
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
        try {
            $validateData = $request->validate([
                'name' => 'required',
                'startShift' => 'date_format:"H:i"|required',
                'endShift' => 'date_format:"H:i"|required',
                'timeBreak' => 'nullable|numeric',
                'status' => 'nullable|boolean',
                'description' => 'nullable',
            ]);

            $validateData['startShift'] = new DateTime("1970-01-01T" . $validateData['startShift'] . ":00");
            $validateData['endShift'] = new DateTime("1970-01-01T" . $validateData['endShift'] . ":00");
            if ($validateData['startShift'] > $validateData['endShift']) {
                $validateData['endShift']->modify('+1 day');
            };


            // dd($validateData);
            Shift::create($validateData);
            return response()->json([
                Shift::orderBy('name')->get()->map(function ($shift) {
                    return [
                        'id' => $shift['id'],
                        'name' => $shift['name'],
                        'startShift' => $shift['startShift'],
                        'endShift' => $shift['endShift'],
                        'timeBreak'=>$shift['timeBreak'],
                        'hours' => $shift['hours'],
                        'status' => $shift['status'],
                        'description' => $shift['description']
                    ];
                })
            ], 200);
        } catch (Exception $e) {
            dd($e);
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'startShift' => 'date_format:"H:i"|required',
            'endShift' => 'date_format:"H:i"|required',
            'timeBreak' => 'nullable|numeric',
            'status' => 'nullable|boolean',
            'description' => 'nullable',
        ]);
        $validateData['startShift'] = new DateTime("1970-01-01T" . $validateData['startShift'] . ":00");
        $validateData['endShift'] = new DateTime("1970-01-01T" . $validateData['endShift'] . ":00");
        if ($validateData['startShift'] > $validateData['endShift']) {
            $validateData['endShift']->modify('+1 day');
        };

        try {
            $shift->update($validateData);
            return response()->json([
                Shift::orderBy('name')->get()->map(function ($shift) {
                    return [
                        'id' => $shift['id'],
                        'name' => $shift['name'],
                        'startShift' => $shift['startShift'],
                        'endShift' => $shift['endShift'],
                        'timeBreak'=>$shift['timeBreak'],
                        'hours' => $shift['hours'],
                        'status' => $shift['status'],
                        'description' => $shift['description']
                    ];
                })
            ], 200);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        try {
            $shift->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
