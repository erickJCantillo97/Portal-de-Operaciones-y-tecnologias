<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Personal\Employee;
use App\Models\Projects\Customer;
use App\Models\Projects\TypeShip;
use App\Models\Quotes\Quote;
use App\Models\Quotes\QuoteStatus;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use App\Notifications\QuoteNotify;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class QuoteController extends Controller
{
    protected $estados = [
        'Proceso',
        'Entregada',
        'Pendiente por Firma',
        'Firmada',
        'No Firmada',
        'Contratada',
    ];
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $quotes = DB::table('quote_last_status_view')->orderBy('consecutive', 'DESC')->get()->map(function ($quote) {
            return [
                'version_id' => $quote->id,
                'name' => $quote->name,
                // 'gerencia' => $quote->gerencia,
                'status' => $quote->status,
                // 'offer_type' => $quote->version->offer_type,
                'get_status' => $this->estados[$quote->status],
                'estimador' => $quote->estimador_name,
                'customer' => $quote->customer_name,
                'version' => $quote->version,
                'created_at' => $quote->created_at,
                'expeted_answer_date' => $quote->expeted_answer_date,
                'consecutive' => $quote->consecutive,
                // 'products' => $quote->version']->quoteTypeShips'],
                // 'total_cost' => collect($quote['version']['total_cost']),
                // 'clases' => implode(', ', collect($quote['version']['quoteTypeShips'])->pluck('name')->toArray())
            ];
        });
        $quote = null;
        if ($request->quote_id) {

            $quote = collect($quotes)->where('version_id', $request->quote_id)->first();
        }


        return Inertia::render('Quotes/Index', compact('quotes', 'quote'));
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
            'coin' => 'nullable|string',
            'observation' => 'nullable|string',
        ]);
        $empleado = Employee::where('Num_SAP', $validateData['estimador_id'])->first();

        $validateData['gerencia'] = auth()->user()->gerencia;

        $validateData['estimador_name'] = $empleado['Usuario'];

        $quote = Quote::create([
            'gerencia' => auth()->user()->gerencia,
            'consecutive' => Quote::max('consecutive') + 1,
            'user_id' => auth()->user()->id,
            'name' => $validateData['name']
        ]); //Creamos en la BD y guardamos la estimacion en una variable
        $version = $request->action == 2 ? $quote->version->version + 1 : 1;

        $quoteVersion = QuoteVersion::create([
            'quote_id' => $quote->id,
            'version' => $version,
            'estimador_id' => $validateData['estimador_id'],
            'estimador_name' => $validateData['estimador_name'],
            'customer_id' => $validateData['customer_id'] ?? null,
            'observation' => $validateData['observation'] ?? '',
            'expeted_answer_date' => $validateData['expeted_answer_date'],
            'offer_type' => $validateData['offer_type'],
            'coin' => $validateData['coin'],
        ])->id; // Creamos una nueva version 1, con el consecutivo de la variable que se utilizó antes

        $quote->current_version_id = $quoteVersion;

        $quote->save();
        if (isset($request->type_ships)) {
            foreach (TypeShip::whereIn('id', $request->type_ships)->get() as $typeShip) {
                QuoteTypeShip::create([
                    'quote_version_id' => $quoteVersion,
                    'type_ship_id' => $typeShip->id,
                    'name' => $typeShip->name,
                ]);
            }
        }
        QuoteStatus::create([
            'status' => 0,
            'user_id' => auth()->user()->id,
            'quote_version_id' => $quoteVersion,
            'fecha' => Carbon::now()
        ]);
        $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $quoteVersion)->first();
        Notification::route('mail', ['ecantillo@cotecmar.com' => $validateData['estimador_name'], 'GBUELVAS@cotecmar.com' => $validateData['estimador_name']])->notify(new QuoteNotify($validateData['estimador_name'], $quote, 'asignament'));
        return response()->json([
            'status' => true,
            'quote' => $quote
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        $quotes = Quote::with('version', 'version.quoteTypeShips')->orderBy('consecutive', 'DESC')->get()->map(function ($quote) {
            return [
                'id' => $quote['id'],
                'name' => $quote['name'],
                'gerencia' => $quote['gerencia'],
                'status' => $quote['version']['status'],
                'offer_type' => $quote['version']['offer_type'],
                'get_status' => $quote['version']['get_status'],
                'estimador' => $quote['version']['estimador_name'],
                'customer' => $quote['version']['customer']['name'] ?? '',
                'version_id' => $quote['version']['id'],
                'version' => $quote['version']['version'],
                'created_at' => $quote['version']['created_at'],
                'expeted_answer_date' => $quote['version']['expeted_answer_date'],
                'consecutive' => $quote['version']['consecutive'],
                'products' => $quote['version']['quoteTypeShips'],
                'total_cost' => collect($quote['version']['total_cost']),
                'clases' => implode(', ', collect($quote['version']['quoteTypeShips'])->pluck('name')->toArray())
            ];
        });
    }

    public function updating(QuoteVersion $quoteVersion)
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
        return $quoteVersion;
        return Inertia::render('Quotes/Form', compact('typeships', 'customers', 'estimadores', 'quoteVersion'));
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

    public function getQuoteStatus()
    {
    }
}
