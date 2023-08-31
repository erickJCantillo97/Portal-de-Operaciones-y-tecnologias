<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Ship;
use Exception;
use App\Http\Requests\StoreShipRequest;
use App\Http\Requests\UpdateShipRequest;
use Illuminate\Http\JsonResponse;

class ShipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $ship = Ship::orderBy('name')->get();

        return response()->json([
            $ship
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
    public function store(StoreShipRequest $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            Ship::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ship $ship)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ship $ship)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipRequest $request, Ship $ship)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $ship->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ship $ship)
    {
        try{
            $ship->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
