<?php

namespace App\Http\Controllers;

use App\Models\UserConfiguration;
use Exception;
use Illuminate\Http\Request;

class UserConfigurationController extends Controller
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
        // $validateData = $request->validate([
        //     'key' => 'required',
        //     'value' => 'required',
        // ]);
        try {
            $key = array_keys($request->all());
            $config = UserConfiguration::firstOrNew([
                'user_id' => auth()->user()->id,
                'key' => $key[0],
            ]);
            $config->value = $request[$key[0]];
            $config->save();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(UserConfiguration $userConfiguration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserConfiguration $userConfiguration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserConfiguration $userConfiguration)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $userConfiguration->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserConfiguration $userConfiguration)
    {
        try {
            $userConfiguration->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
