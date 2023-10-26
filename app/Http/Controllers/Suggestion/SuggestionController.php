<?php

namespace App\Http\Controllers\Suggestion;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;

class SuggestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $validateData = $request->validate([
            'type' => 'nullable|string',
            'date' => 'nullable|date'
        ]);
        $suggestion = [];
        if ($request->user()->username == 'gbuelvas') {
            if (!isset($validateData['type']) and !isset($validateData['date'])) {
                // dd('llega ambas null');
                $suggestion = Inertia::render('Suggestions', [
                    'suggestions' => Suggestion::with('User')
                        ->get(),
                    'permission' => true
                ]);
            } else if (!isset($validateData['type']) and isset($validateData['date'])) {
                // dd('llega type = null');
                $suggestion = Inertia::render('Suggestions', [
                    'suggestions' => Suggestion::with('User')
                        ->whereDate('created_at', '=', $validateData['date'])
                        ->get(),
                    'permission' => true
                ]);
            } else if (isset($validateData['type']) and !isset($validateData['date'])) {
                // dd('llega date = null');
                $suggestion = Inertia::render('Suggestions', [
                    'suggestions' => Suggestion::with('User')
                        ->where('type', '=', $validateData['type'])
                        ->get(),
                    'permission' => true
                ]);
            } else {
                // dd('llega ambas definidas');
                $suggestion = Inertia::render('Suggestions', [
                    'suggestions' => Suggestion::with('User')
                        ->where('type', '=', $validateData['type'])
                        ->whereDate('created_at', '=', $validateData['date'])
                        ->get(),
                    'permission' => true
                ]);
            }
        } else {
            $suggestion = Inertia::render('Suggestions', [
                'suggestions' => Suggestion::with('User')
                    ->where('user_id', $request->user()->id)
                    ->get()
            ]);
        }
        return $suggestion;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            Suggestion::where('user_id', auth()->user()->id)
                ->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validateData = $request->validate([
                'details' => 'required',
                'type' => 'required',
                'urlAddress' => 'required'
            ]);
            $validateData['user_id'] = $request->user()->id;
            $validateData['capture'] = Storage::putFileAs(
                'public/Suggestion/',
                $request->capture,
                $validateData['user_id'] . '.' . mt_rand(10000000, 99999999) . '.' . 'png'
            );
            Suggestion::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Suggestion $suggestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Suggestion $suggestion)
    {
        $validateData = $request->validate([
            'answer' => 'required'
        ]);
        $validateData['status'] = false;
        try {
            $suggestion->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Suggestion $suggestion)
    {
        try {
            $suggestion->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
