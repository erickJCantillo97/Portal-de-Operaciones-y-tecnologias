<?php

namespace App\Http\Controllers;

use App\Models\Projects\QuoteTypeShip;
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

        try{
            QuoteTypeShip::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
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
    public function update(Request $request, QuoteTypeShip $quoteTypeShip)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $quoteTypeShip->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuoteTypeShip $quoteTypeShip)
    {
        try{
            $quoteTypeShip->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
