<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use App\Models\Projects\TypeShip;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteStatus;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quotes = Quote::with('version', 'version.quoteTypeShips')->orderBy('id')->get()->map(function ($quote) {
            return [
                'id' => $quote['id'],
                'name' => $quote['name'],
                'gerencia' => $quote['gerencia'],
                'status' => $quote['version']['get_status'],
                'estimador' => $quote['version']['estimador_name'],
                'customer' => $quote['version']['customer']['name'],
                'version_id' => $quote['version']['id'],
                'created_at' => $quote['version']['created_at'],
                'expeted_answer_date' => $quote['version']['expeted_answer_date'],
                'consecutive' => str_pad($quote['consecutive'], 3, 0, STR_PAD_LEFT) . '-' . $quote['version']['version'] . '-2023',
                'products' => $quote['version']['quoteTypeShips'],
                'clases' => implode(', ', collect($quote['version']['quoteTypeShips'])->pluck('name')->toArray())
            ];
        });

        return Inertia::render('Quotes/Index', compact('quotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
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
        return Inertia::render('Quotes/Form', compact('typeships', 'customers', 'estimadores'));
    }

    /**
     * creamos 3 nuevos registros para completar la solicitud de estimación
     * Aqui se crea un consecutivo, una version y la relación entre la verison y las clases, esta funcion solo debe ser usada cuando se va a crear una nueva estimacion con un nuevo consecutivo
     */
    public function store(Request $request)
    {
        // dd($request);
        $validateData = $request->validate([
            'customer_id' => 'nullable',
            'name' => 'required',
            'estimador_id' => 'required|numeric',
            'expeted_answer_date' => 'required|date',
            'offer_type' => 'nullable',
            'type_ships' => 'nullable|array',
            'observation' => 'nullable|string',
        ]);
        $empleado = collect(searchEmpleados('Num_SAP', $validateData['estimador_id']))->first();
        try {
            $validateData['gerencia'] = auth()->user()->gerencia;

            $validateData['estimador_name'] = $empleado['Usuario'];

            $quote = Quote::create([
                'gerencia' => auth()->user()->gerencia,
                'consecutive' => Quote::max('consecutive') + 1,
                'user_id' => auth()->user()->id,
                'name' => $validateData['name']
            ]); //Creamos en la BD y guardamos la estimacion en una variable

            $quoteVersion = QuoteVersion::create([
                'quote_id' => $quote->id,
                'version' => 1,
                'estimador_id' => $validateData['estimador_id'],
                'customer_id' => $validateData['customer_id'],
                'observation' => $validateData['observation'] ?? '',
                'estimador_name' => $validateData['estimador_name'],
                'expeted_answer_date' => $validateData['expeted_answer_date'],
                'offer_type' => $validateData['offer_type'],
            ])->id; // Creamos una nueva version 1, con el consecutivo de la variable que se utilizó antes

            $quote->current_version_id = $quoteVersion;
            $quote->save();

            foreach (TypeShip::whereIn('id', $request->type_ships)->get() as $typeShip) {
                QuoteTypeShip::create([
                    'quote_version_id' => $quoteVersion,
                    'type_ship_id' => $typeShip->id,
                    'name' => $typeShip->name,
                ]);
            }
            QuoteStatus::create([
                'status' => 0,
                'user_id' => auth()->user()->id,
                'quote_version_id' => $quoteVersion,
                'fecha' => Carbon::now()
            ]);

            $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $quoteVersion)->first();

            return response()->json([
                'status' => true,
                'quote' => $quote
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Ocurrió un error al crear la Cotizacion",
                'errorCode' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        $validateData = $request->validate([
            'ship_id' => 'nullable',
            'cost' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Quote/',
                    $request->pdf,
                    $validateData['code'] . '.' . $request->pdf->getClientOriginalExtension()
                );
            }
            $quote->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        try {
            $quote->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
