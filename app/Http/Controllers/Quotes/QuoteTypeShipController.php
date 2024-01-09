<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Quotes\QuoteTypeShip;
use App\Models\Quotes\QuoteVersion;
use Exception;
use Illuminate\Http\Request;

class QuoteTypeShipController extends Controller
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
    public function store(Request $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            QuoteTypeShip::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuoteTypeShip $quoteTypeShip)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuoteTypeShip $quoteTypeShip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuoteVersion $quoteVersion)
    {
        try {
            foreach ($request->type_ships as $type_ship) {
                $id = $type_ship['type_ship_id'];
                $datos = array_diff_key($type_ship,  array_flip(['id', 'created_at', 'updated_at']));
                QuoteTypeShip::where('quote_version_id',  $type_ship['quote_version_id'])->where('type_ship_id', $id)->first()->update(
                    $datos
                );
            }


            $quote = QuoteVersion::with('quote', 'quoteTypeShips')->where('id', $quoteVersion->id)->first();
            return response()->json([
                'status' => true,
                'quote' => $quote
            ]);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuoteTypeShip $quoteTypeShip)
    {
        try {
            $quoteTypeShip->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
