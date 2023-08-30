<?php

namespace App\Http\Controllers;


use Exception;
use App\Http\Requests\StoreQuotAuthRequest;
use App\Http\Requests\UpdateQuotAuthRequest;
use App\Models\Projects\QuotAuthorization;
use Illuminate\Support\Facades\Request;

class QuotAuthorizationController extends Controller
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
            QuotAuthorization::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuotAuthorization $quotAuth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuotAuthorization $quotAuth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuotAuthorization $quotAuth)
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
    public function destroy(QuotAuthorization $quotAuth)
    {
        try{
            $quotAuth->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
