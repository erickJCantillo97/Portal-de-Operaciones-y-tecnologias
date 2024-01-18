<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use App\Models\Projects\TypeShip;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use Carbon\Carbon;
use Exception;
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
    public function store(Request $request, QuoteVersion $quoteVersion)
    {


        $validateData = $request->validate([
            'estimador_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'observation' => 'nullable|string',
            'expeted_answer_date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
            'offer_type' => 'string',
            'coin' => 'string',
        ]);
        $empleado = collect(searchEmpleados('Num_SAP', $validateData['estimador_id']))->first();
        $quote = Quote::where('id', $quoteVersion->quote_id)->first();

        $validateData['estimador_name'] = $empleado['Usuario'];

        $newQuoteVersion = QuoteVersion::create([
            'quote_id' => $quote->id,
            'version' => $quote->version->version + 1,
            'estimador_id' => $validateData['estimador_id'],
            'customer_id' => $validateData['customer_id'],
            'observation' => $validateData['observation'],
            'coin' => $validateData['coin'],
            'estimador_name' => $validateData['estimador_name'],
            'expeted_answer_date' => $validateData['expeted_answer_date'],
            'offer_type' => $validateData['offer_type'],
        ]);
        $quote->current_version_id = $newQuoteVersion->id;
        $quote->save();
        // Creamos una nueva version 1, con el consecutivo de la variable que se utilizó antes
        foreach (TypeShip::whereIn('id', $request->type_ships)->get() as $typeShip) {
            $quoteTypeShip = QuoteTypeShip::where('type_ship_id', $typeShip->id)->where('quote_version_id', $quoteVersion->id)->first();
            // Añadir una nueva clase a una version nueva
            QuoteTypeShip::create([
                'quote_version_id' => $newQuoteVersion->id,
                'type_ship_id' => $typeShip->id,
                'name' => $typeShip->name,
                'scope' => $quoteTypeShip->scope ?? null,
                'maturity' => $quoteTypeShip->maturity ?? null,
                'units' => $quoteTypeShip->units ?? null,
                'rate_buy_usd' => $quoteTypeShip->rate_buy_usd ?? null,
                'rate_buy_eur' => $quoteTypeShip->rate_buy_eur ?? null,
                'iva' => $quoteTypeShip->iva ?? null,
                'white_paper' => $quoteTypeShip->white_paper ?? null,
                'white_paper_number' => $quoteTypeShip->white_paper_number ?? null,
            ]);
        }

        $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $newQuoteVersion->id)->first();

        return response()->json([
            'status' => true,
            'quote' => $quote
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function updating(QuoteVersion $quote)
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
        $action = 2;
        $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $quote->id)->first();
        return Inertia::render('Quotes/Form', compact('typeships', 'customers', 'estimadores', 'quote', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $typeships = TypeShip::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        // $estimadores = getPersonalGerenciaOficina('GECON', 'DEEST')->map(function ($estimador) {
        //     return [
        //         'user_id' => $estimador['Num_SAP'],
        //         'name' => $estimador['Nombres_Apellidos'],
        //         'email' => $estimador['Correo']
        //     ];
        // })->toArray();
        $estimadores =  [];
        $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $id)->first();
        return Inertia::render('Quotes/Form', compact('typeships', 'customers', 'estimadores', 'quote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateData = $request->validate([
            'estimador_id' => 'required|numeric',
            'customer_id' => 'required|numeric',
            'observation' => 'nullable|string',
            'expeted_answer_date' => 'required|date',
            'offer_type' => 'string',
        ]);
        $empleado = collect(searchEmpleados('Num_SAP', $validateData['estimador_id']))->first();

        $validateData['estimador_name'] = $empleado['Usuario'];
        $quoteVersion = QuoteVersion::findOrFail($id)->update([
            'estimador_id' => $validateData['estimador_id'],
            'customer_id' => $validateData['customer_id'],
            'observation' => $validateData['observation'],
            'estimador_name' => $validateData['estimador_name'],
            'expeted_answer_date' => $validateData['expeted_answer_date'],
            'offer_type' => $validateData['offer_type'],
        ]);

        QuoteTypeShip::where('quote_version_id', $id)->whereNotIn('type_ship_id', $request->type_ships)->delete();

        foreach (TypeShip::whereIn('id', $request->type_ships)->get() as $typeShip) {
            QuoteTypeShip::firstOrCreate([
                'quote_version_id' => $id,
                'type_ship_id' => $typeShip->id,
                'name' => $typeShip->name,
            ]);
        }

        $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $id)->first();
        return response()->json([
            'status' => true,
            'quote' => $quote
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($quoteVersion_id)
    {
        $quoteVersion = QuoteVersion::find($quoteVersion_id);

        try {
            if ($quoteVersion->id == $quoteVersion->quote->current_version_id) {
                $quoteVersion->delete();
                $ultimaVersion = QuoteVersion::where('quote_id', $quoteVersion->quote_id)->orderBy('version', 'DESC')->first();
                if (!isset($ultimaVersion)) {
                    Quote::find($quoteVersion->quote_id)->delete();
                    return back()->with(['message' => 'Estimacion eliminada']);
                }
                Quote::find($quoteVersion->quote_id)->update([
                    'current_version_id' => $ultimaVersion->id
                ]);
                return back()->with(['message' => 'Estimacion eliminada']);
            }
            QuoteTypeShip::where('quote_version_id', $quoteVersion->id)->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
