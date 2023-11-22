<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\TypeShip;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TypeShipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeShips = TypeShip::get();
        return Inertia::render('TypeShips', [
            'typeShips' => $typeShips,
        ]);
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
            'name' => 'required|string|unique:type_ships,name',
            'type' => 'nullable|string',
            'disinger' => 'nullable|string',
            'hull_material' => 'nullable', //material del casco
            'length' => 'nullable', //eslra
            'breadth' => 'nullable', //Manga
            'draught' => 'nullable', //calado de diseño
            'depth' => 'nullable', //punta
            'full_load' => 'nullable',
            'light_ship' => 'nullable',
            'power_total' => 'nullable',
            'propulsion_type' => 'nullable',
            'velocity' => 'nullable',
            'autonomias' => 'nullable',
            'autonomy' => 'nullable',
            'crew' => 'nullable',
            'GT' => 'nullable',
            'CGT' => 'nullable',
            'bollard_pull' => 'nullable',
            'clasification' => 'nullable',
            'render' => 'nullable',
        ]);

        try {
            if ($request->render != null) {
                $validateData['render'] = Storage::putFileAs(
                    'public/TypeShips/',
                    $request->render,
                    $validateData['name'].'.'.$request->render->getClientOriginalExtension()
                );
            }
            TypeShip::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TypeShip $typeShip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeShip $typeShip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeShip $typeShip)
    {
        $validateData = $request->validate([
            'name' => 'required|string|unique:type_ships,name,' . $typeShip->id,
            'type' => 'nullable|string',
            'disinger' => 'nullable|string',
            'hull_material' => 'nullable', //material del casco
            'length' => 'nullable', //eslra
            'breadth' => 'nullable', //Manga
            'draught' => 'nullable', //calado de diseño
            'depth' => 'nullable', //punta
            'full_load' => 'nullable',
            'light_ship' => 'nullable',
            'power_total' => 'nullable',
            'propulsion_type' => 'nullable',
            'velocity' => 'nullable',
            'autonomias' => 'nullable',
            'autonomy' => 'nullable',
            'crew' => 'nullable',
            'GT' => 'nullable',
            'CGT' => 'nullable',
            'bollard_pull' => 'nullable',
            'clasification' => 'nullable',
            'render' => 'nullable',
        ]);

        try {
            $typeShip->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeShip $typeShip)
    {
        try {
            $typeShip->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
