<?php

namespace App\Http\Controllers\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\WareHouse\Consumable;
use App\Models\WareHouse\ConsumableSAP;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConsumableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('WareHouse/Consumable/Index');
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $codigo = $request->codigo_salida;

        $materiales = ConsumableSAP::where('MBLNR', 'like', '%' . $codigo . '%')->get();

        if ($materiales) {
            return response()->json([
                'consumibles' => $materiales,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            Consumable::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Consumable $consumable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consumable $consumable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Consumable $consumable)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $consumable->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Consumable $consumable)
    {
        try {
            $consumable->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
