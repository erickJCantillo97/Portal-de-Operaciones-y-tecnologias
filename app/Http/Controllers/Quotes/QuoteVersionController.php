<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use App\Models\Projects\TypeShip;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteVersionController extends Controller
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
    public function store(Request $request, Quote $quote)
    {
        $validateData = $request->validate([
            'estimador_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'observation' => 'nullable|string',
            'expeted_answer_date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
            'offer_type' => 'string',
        ]);
        $quoteVersion = QuoteVersion::create([
            'quote_id' => $quote->id,
            'version' => $quote->version->version + 1,
            'estimador_id' => $validateData['estimador_id'],
            'customer_id' => $validateData['customer_id'],
            'observation' => $validateData['observation'],
            'estimador_name' => $validateData['estimador_name'],
            'expeted_answer_date' => $validateData['expeted_answer_date'],
            'offer_type' => $validateData['offer_type'],
        ]);
        $quote->current_version_id = $quoteVersion->id;
        $quote->save();
        // Creamos una nueva version 1, con el consecutivo de la variable que se utilizó antes
        foreach (TypeShip::whereIn('id', $request->type_ships) as $typeShip) {
            $quoteTypeShip = QuoteTypeShip::create([
                'quote_version_id' => $quoteVersion->id,
                'type_ship_id' => $typeShip->id,
                'name' => $typeShip->name,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $typeships = TypeShip::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $estimadores = getPersonalGerenciaOficina('GECON', 'DEEST')->map(function ($estimador) {
            return [
                'user_id' => $estimador['Num_SAP'],
                'name' => $estimador['Nombres_Apellidos'],
                'email' => $estimador['Correo']
            ];
        })->toArray();
        $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $id)->first();
        return Inertia::render('Quotes/Form', compact('typeships', 'customers', 'estimadores','quote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
