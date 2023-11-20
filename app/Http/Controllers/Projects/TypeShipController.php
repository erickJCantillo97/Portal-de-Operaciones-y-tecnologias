<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\TypeShip;
use Exception;
use Illuminate\Http\Request;
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
            'name' => 'required|string|unique:type_ships,',
            // $table->string('type')->nullable();
            // $table->string('disinger')->nullable();
            // $table->enum('hull_material', ['ACERO', 'ALUMINIO', 'MATERIALES COMPUESTOS'])->nullable(); //material del casco
            // $table->double('length')->nullable(); //eslra
            // $table->double('breadth')->nullable(); //Manga
            // $table->double('draught')->nullable(); //calado de diseño
            // $table->double('depth')->nullable(); //punta
            // $table->double('full_load')->nullable();
            // $table->double('light_ship')->nullable();
            // $table->double('power_total')->nullable();
            // $table->string('propulsion_type')->nullable();
            // $table->string('velocity')->nullable();
            // $table->double('autonomias')->nullable();
            // $table->double('autonomy')->nullable();
            // $table->double('crew')->nullable();
            // $table->double('GT')->nullable();
            // $table->double('CGT')->nullable();
            // $table->double('bollard_pull')->nullable();
            // $table->string('clasification')->nullable();
            // $table->string('render')->nullable();
        ]);

        try {
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
            //
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
