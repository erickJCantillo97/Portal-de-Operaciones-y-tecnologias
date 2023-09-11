<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Contract;
use App\Models\Projects\Customer;
use App\Models\Projects\Ship;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::with('customer', 'ship')->orderBy('name')->get();
        $ships = Ship::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return Inertia::render('Project/Contracts',
            [
                'contracts' => $contracts,
                'customers' => $customers,
                'ships' => $ships,
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
        // dd($request);
        $validateData = $request->validate([
            'customer_id' => 'nullable',
            'ship_id' => 'nullable',
            'name' => 'required',
            'cost' => 'required|numeric|gt:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/contract/',
                    $request->pdf,
                    $validateData['name'].'.'.$request->pdf->getClientOriginalExtension()
                );
            }
            Contract::create($validateData);

            return back()->with(['message' => 'Contrato creado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear el contrato: '.$e->getMessage()], 500);
        }

        return redirect('contracts.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validateData = $request->validate([
            'name' => 'required',
            'cost' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        try {
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/contract/',
                    $request->pdf,
                    $validateData['name'].'.'.$request->pdf->getClientOriginalExtension()
                );
            }
            $contract->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        try {
            $contract->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
