<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Projects\Quote;
use App\Models\Projects\Ship;
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
        $quotes = Quote::with('ship')->orderBy('id')->get();
        $ships = Ship::orderBy('name')->get();

        return Inertia::render('Project/Quotes',
            [
                'quotes' => $quotes,
                'ships' => $ships,
            ]
        );
        // return response()->json([
        //     $quote
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
            'ship_id' => 'nullable',
            'code' => 'required|unique:quotes,code',
            'cost' => 'required|numeric|gt:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
            $validateData['name'] = $validateData['code'];

            if ($request->pdf != null) {
                $validateData['file'] = Storage::putFileAs(
                    'public/Quote/',
                    $request->pdf,
                    $validateData['code'].'.'.$request->pdf->getClientOriginalExtension()
                );
            }
            Quote::create($validateData);

            return back()->with(['message' => 'Cotizacion creada correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors(['message' => 'Ocurrió un error al crear la Cotizacion: '.$e->getMessage()], 500);
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
                    $validateData['code'].'.'.$request->pdf->getClientOriginalExtension()
                );
            }
            $quote->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
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
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
