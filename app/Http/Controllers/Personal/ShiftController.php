<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            Shift::orderBy('name')->get()->map(function ($shift) {
                return [
                    'id' => $shift['id'],
                    'name' => $shift['name'],
                    'startShift' => $shift['startShift'],
                    'endShift' => $shift['endShift'],
                    'startBreak' => $shift['startBreak'],
                    'endBreak' => $shift['endBreak'],
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
        $validateData = $request->validate([
            'name' => 'required',
            'startShift' => 'date|required',
            'endShift' => 'date|required',
            'startBreak' => 'date|required',
            'endBreak' => 'date|required',
            'status' => 'nullable|boolean',
            'description' => 'nullable',
        ]);
        if ($validateData['startShift'] < $validateData['endShift']) {
            $validateData['startShift'] < -'1970-01-01T' + $validateData['startShift'];
            $validateData['endShift'] < -'1970-01-02T' + $validateData['endShift'];
        } else {
            $validateData['startShift'] < -'1970-01-01T' + $validateData['startShift'];
            $validateData['endShift'] < -'1970-01-01T' + $validateData['endShift'];
        }
        if ($validateData['startBreak'] < $validateData['endBreak']) {
            $validateData['startBreak'] < -'1970-01-01T' + $validateData['startBreak'];
            $validateData['endBreak'] < -'1970-01-02T' + $validateData['endBreak'];
        } else {
            $validateData['startBreak'] < -'1970-01-01T' + $validateData['startBreak'];
            $validateData['endBreak'] < -'1970-01-01T' + $validateData['endBreak'];
        }

        try {
            Shift::create($validateData);
        } catch (Exception $e) {
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
            'startShift' => 'date|required',
            'endShift' => 'date|required',
            'startBreak' => 'nullable|date',
            'endBreak' => 'nullable|date',
            'status' => 'nullable|boolean',
            'description' => 'nullable',
        ]);

        try {
            $shift->update($validateData);
            return response()->json([
                Shift::orderBy('name')->get()->map(function ($shift) {
                    return [
                        'id' => $shift['id'],
                        'name' => $shift['name'],
                        'startShift' => $shift['startShift'],
                        'endShift' => $shift['endShift'],
                        'startBreak' => $shift['startBreak'],
                        'endBreak' => $shift['endBreak'],
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
