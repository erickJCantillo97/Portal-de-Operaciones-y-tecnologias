<?php

namespace App\Http\Controllers;

use App\Models\GD\FileManagerDocument;
use Exception;
use Illuminate\Http\Request;

class FileManagerDocumentController extends Controller
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
            FileManagerDocument::create($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(FileManagerDocument $fileManagerDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FileManagerDocument $fileManagerDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FileManagerDocument $fileManagerDocument)
    {
        $validateData = $request->validate([
            //
        ]);

        try{
            $fileManagerDocument->update($validateData);
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : '.$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FileManagerDocument $fileManagerDocument)
    {
        try{
            $fileManagerDocument->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : '.$e);
        }
    }
}
