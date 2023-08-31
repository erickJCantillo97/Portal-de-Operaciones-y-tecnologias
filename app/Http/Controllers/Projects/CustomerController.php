<?php

namespace App\Http\Controllers;

use App\Models\customer;
use Exception;
use App\Http\Requests\StorecustomerRequest;
use App\Http\Requests\UpdatecustomerRequest;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $customer = customer::orderBy('name')->get();

        return response()->json([
            $customer
        ], 200);
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
    public function store(StorecustomerRequest $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            customer::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatecustomerRequest $request, customer $customer)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $customer->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(customer $customer)
    {
        try {
            $customer->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
