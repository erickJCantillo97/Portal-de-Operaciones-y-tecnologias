<?php

namespace App\Http\Controllers;

use App\Models\NotificationUser;
use Exception;
use Illuminate\Http\Request;

class NotificationUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = NotificationUser::orderBy('date')->take(5)->get();
        return response()->json([
            'notifications' => $notifications
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
    public function store(Request $request)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            NotificationUser::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NotificationUser $notificationUser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NotificationUser $notificationUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NotificationUser $notificationUser)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $notificationUser->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationUser $notificationUser)
    {
        try {
            $notificationUser->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
