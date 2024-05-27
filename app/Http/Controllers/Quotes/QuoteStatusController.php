<?php

namespace App\Http\Controllers\Quotes;

use App\Http\Controllers\Controller;
use App\Models\Quotes\QuoteStatus;
use App\Models\Quotes\QuoteVersion;
use Exception;
use Illuminate\Http\Request;

class QuoteStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = QuoteStatus::orderBy('fecha', 'DESC')->with('user')->where('quote_version_id', $request->id)->get();
        return response()->json([
            'status' => $status
        ]);
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
            'status' => 'required',
            'quote_version_id' => 'required',
            'fecha' => 'required'
        ]);
        $validateData['user_id'] = auth()->user()->id;

        try {
            if ($validateData['status'] > 0) {
                QuoteVersion::find($validateData['quote_version_id'])->update([
                    'estimador_anaswer_date' => $validateData['fecha'],
                ]);
            }
            QuoteStatus::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(QuoteStatus $quoteStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuoteStatus $quoteStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuoteStatus $quoteStatus)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $quoteStatus->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuoteStatus $quoteStatus)
    {
        try {
            $quoteStatus->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
