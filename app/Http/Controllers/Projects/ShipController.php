<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use App\Models\Projects\Ship;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ShipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){

     if (isset($request->id)){

        $ships = Ship::with('customer')->where('customer_id', $request->id)->get();
        // dd($ships);
        return Inertia::render('Project/Ships',
            [
                'ships' => $ships,
                'customer'=> Customer::find($request->id)
            ]
        );
    }else{
        $ships = Ship::with('customer')->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        return Inertia::render('Project/Ships',
            [
                'ships' => $ships,
                'customers' => $customers,
            ]
        );
    }
        // return response()->json([
        //     $ship
        // ], 200);
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
        // dd($request);
        $validateData = $request->validate([
            'customer_id' => 'nullable',
            'name' => 'required',
            'type' => 'nullable',
            'quilla' => 'nullable|numeric|gt:0',
            'pantoque' => 'nullable|numeric|gt:0',
            'eslora' => 'nullable|numeric|gt:0',
            'details' => 'nullable',
            'image' => 'nullable'
        ]);

        try {
            if ($request->image != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Ship/',
                    $request->image,
                    $validateData['name'] . "." . $request->image->getClientOriginalExtension()
                );
            };
            Ship::create($validateData);

            return back()->with(['message' => 'Buque creado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el Buque: '.$e->getMessage()], 500);
        }

        return redirect('ships.index');
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
    public function update(Request $request, Ship $ship)
    {
        $validateData = $request->validate([
            'customer_id' => 'nullable',
            'name' => 'required',
            'type' => 'nullable',
            'quilla' => 'nullable|numeric|gt:0',
            'pantoque' => 'nullable|numeric|gt:0',
            'eslora' => 'nullable|numeric|gt:0',
            'details' => 'nullable',
        ]);
        // dd($validateData);
        
        try {
            if ($request->image != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Ship/',
                    $request->image,
                    $validateData['name'] . "." . $request->image->getClientOriginalExtension()
                );
            };
            $ship->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ship $ship)
    {
        try {
            $ship->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
