<?php

namespace App\Http\Controllers\SWBS;

use App\Http\Controllers\Controller;
use App\Models\SWBS\SubSystem;
use Exception;
use Illuminate\Http\Request;

class SubSystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subsystems = SubSystem::with('system')->get();
        if ($request->system) {
            $subsystems = SubSystem::where('system_id', $request->system)->orderBy('code')->get();
        }

        return response()->json(
            $subsystems,
            200
        );
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
            'system_id' => 'required|numeric',
            'code' => 'required|numeric|unique:sub_systems',
            'name' => 'required',
        ]);

        try {
            SubSystem::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubSystem $subSystem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubSystem $subSystem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubSystem $subSystem)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        try {
            SubSystem::find($request->id)->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubSystem $subSystem)
    {
        try {
            $subSystem->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
