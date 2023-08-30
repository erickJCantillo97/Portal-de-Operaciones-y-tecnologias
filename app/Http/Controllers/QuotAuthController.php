<?php

namespace App\Http\Controllers;

use App\Models\QuotAuth;
use Exception;
use App\Http\Requests\StoreQuotAuthRequest;
use App\Http\Requests\UpdateQuotAuthRequest;

class QuotAuthController extends Controller
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
    public function store(StoreQuotAuthRequest $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            QuotAuth::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuotAuth $quotAuth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuotAuth $quotAuth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotAuthRequest $request, QuotAuth $quotAuth)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $quotAuth->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuotAuth $quotAuth)
    {
        try{
            $quotAuth->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
