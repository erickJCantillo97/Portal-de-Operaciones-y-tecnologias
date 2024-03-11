<?php

namespace App\Http\Controllers\Projects;

use App\Exports\ProjectsDetailsExport;
use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use App\Models\Projects\Ship;
use App\Models\Projects\TypeShip;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ShipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (isset($request->id)) {

            $ships = Ship::with('customer', 'typeShip', 'projectsShip')->where('customer_id', $request->id)->get();
            $data = [
                'ships' => $ships,
                'customer' => Customer::find($request->id),
                'typeShips' => TypeShip::get(),
            ];
            return Inertia::render(
                'Project/Ships',
                $data
            );
        }

        $ships = Ship::with('customer', 'typeShip', 'projectsShip')->orderBy('name')->get();


        if ($request->expectsJson()) {
            return response()->json([
                'ships' => $ships,
            ], 200);
        }

        $customers = Customer::orderBy('name')->get();
        $typeShips = TypeShip::get();

        return Inertia::render('Project/Ships', compact('ships', 'customers', 'typeShips'));
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
            // 'customer_id' => 'nullable',
            'name' => 'required',
            'type_ship_id' => 'nullable',
            'quilla' => 'nullable|numeric|gt:0',
            'pantoque' => 'nullable|numeric|gt:0',
            'acronyms' => 'nullable',
            'idHull' => 'nullable|unique:ships,idHull',
        ]);

        try {
            if ($request->image != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Ship/',
                    $request->image,
                    $validateData['name'] . '.' . $request->image->getClientOriginalExtension()
                );
            }
            Ship::create($validateData);

            return back()->with(['message' => 'Unidad creada correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el Buque: ' . $e->getMessage()], 500);
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

    public function export()
    {

        return Excel::download(new ProjectsDetailsExport, 'Buques.xlsx');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ship $ship)
    {
        // dd($request);
        $validateData = $request->validate([
            'customer_id' => 'nullable',
            'name' => 'required',
            'type_ship_id' => 'nullable',
            'quilla' => 'nullable|numeric|gt:0',
            'pantoque' => 'nullable|numeric|gt:0',
            'acronyms' => 'nullable',
            'idHull' => 'nullable',
        ]);
        // dd($validateData);

        try {
            if ($request->image != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Ship/',
                    $request->image,
                    $validateData['name'] . '.' . $request->image->getClientOriginalExtension()
                );
            }
            $ship->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
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
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
