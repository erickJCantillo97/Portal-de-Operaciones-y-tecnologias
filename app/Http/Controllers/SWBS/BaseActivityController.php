<?php

namespace App\Http\Controllers\SWBS;

use App\Http\Controllers\Controller;
use App\Models\SWBS\BaseActivity;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseActivityController extends Controller
{
   // Illuminate\Http\JsonResponse
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $baseActivity = BaseActivity::orderBy('name')->get();

        return response()->json([
                $baseActivity
            ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'Holaaaaaaaaaa';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|unique:base_activities,name'
        ]);

        try{
            BaseActivity::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BaseActivity $baseActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BaseActivity $baseActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BaseActivity $baseActivity)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $baseActivity->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BaseActivity $baseActivity)
    {
        try{
            $baseActivity->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
