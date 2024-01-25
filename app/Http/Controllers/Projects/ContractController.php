<?php

namespace App\Http\Controllers\Projects;

use App\Events\ContractEvent;
use App\Http\Controllers\Controller;
use App\Models\Projects\Contract;
use App\Models\Projects\Customer;
use App\Models\Quotes\QuoteVersion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::with('customer', 'quote')->orderBy('contract_id')->get();
        $customers = Customer::orderBy('name')->get();
        $quotes = QuoteVersion::with('customer')->get()->filter(function ($quote) {
            return $quote['get_status'] === 'Contratada';
        });
        return Inertia::render('Project/Contracts', compact('contracts', 'customers', 'quotes'));
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
            'contract_id' => 'required|unique:contracts,contract_id',
            'subject' => 'nullable',
            'type_of_sale' => 'nullable',
            'supervisor' => 'nullable',
            'quote_id' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'state' => 'nullable',
            'file' => 'nullable',
        ]);
        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/contract/',
                    $request->pdf,
                    $validateData['contract_id'] . '.' . $request->pdf->getClientOriginalExtension()
                );
            }
            Contract::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrio un Error Inesperado']);
        }

        return back()->with(['message' => 'Contrato Guardado']);
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
            'contract_id' => 'required|unique:contracts,contract_id,' . $contract->id,
            'subject' => 'nullable',
            'customer_id' => 'nullable',
            'manager_id' => 'nullable',
            'type_of_sale' => 'nullable',
            'supervisor' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'currency' => 'nullable',
            'cost' => 'nullable|numeric',
            'state' => 'nullable',
            'file' => 'nullable',
        ]);

        try {
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/contract/',
                    $request->pdf,
                    $validateData['name'] . '.' . $request->pdf->getClientOriginalExtension()
                );
            }
            $contract->update($validateData);
            event(new ContractEvent($contract, 'updated'));
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        try {
            $contract->delete();
            event(new ContractEvent($contract, 'deleted'));
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }

    public function getContracts()
    {
        return response()->json([
            'contracts' => Contract::orderBy('contract_id')->get(),
        ], 200);
    }
}
