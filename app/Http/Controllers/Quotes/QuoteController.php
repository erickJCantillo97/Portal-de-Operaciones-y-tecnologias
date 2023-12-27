<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Projects\Customer;
use App\Models\Projects\Ship;
use App\Models\Projects\TypeShip;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
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
        $quotes = Quote::orderBy('id')->get();
        return Inertia::render('Quotes/Index', compact('quotes'));
        // return response()->json([
        //     $quote
        // ], 200);
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
            'customer_id' => 'nullable|exists:id',
            'name' => 'request',
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
                'version' => $quote->current_version_id,
                'estimador_id' => $validateData['estimador_id'],
                'customer_id' => $validateData['customer_id'],
                'observation' => $validateData['observation'],
                'estimador_name' => $validateData['estimador_name'],
                'expeted_answer_date' => $validateData['expeted_answer_date'],
                'offer_type' => $validateData['offer_type'],
            ]); // Creamos una nueva version 1, con el consecutivo de la variable que se utilizó antes
            $quotesTypeShips = [];
            foreach (TypeShip::whereIn('id', $request->type_ships) as $typeShip) {
                $quoteTypeShip = QuoteTypeShip::create([
                    'quote_version_id' => $quoteVersion->id,
                    'type_ship_id' => $typeShip->id,
                    'name' => $typeShip->name,
                ]);
                array_push($quotesTypeShips, $quoteTypeShip); //se Añaden las relaciones creadas a un array para devolverlos al front una vez sean creados
            }
            return back()->with(['message' => 'Oferta creada correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear la Cotizacion: ' . $e->getMessage()], 500);
        }

        return redirect('ships.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        //
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
