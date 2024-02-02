<?php

namespace App\Http\Controllers\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\WareHouse\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Category::level('Grupo')->get();
        $subgroups = Category::level('Subgrupo')->with('padre')->get();
        $categories = Category::has('padre')->with('padre', 'padre.padre')->where('level', 'Descripcion')
            ->get();
        return Inertia::render('WareHouse/Categories', [
            'categories' => $categories,
            'subgroups' => $subgroups,
            'groups' => $groups,
        ]);
    }


    public function getDataAnterior()
    {
        // $cas = DB::connection('sqlsrv_anterior')->table('categorias')->get();
        // foreach ($cas as $c) {
        //     $padre = DB::connection('sqlsrv_anterior')->table('categorias')->where('id', $c->categoria_id)->first();
        //     Category::create([
        //         'user_id' => auth()->user()->id,
        //         'name' => $c->name,
        //         'letter' => $c->letra,
        //         'level' => $c->nivel,
        //         'category_id' =>  $padre != null ? (Category::where('name', $padre->name)->first()->id ?? 0) : 0,
        //     ]);
        // }
        Category::where('level', 'Sub Grupo')->update(['level' => 'Subgrupo']);
        return Category::get();
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
            'category_id' => 'nullable',
            'letter' => 'nullable',
            'name' => 'required',
            'level' => 'required|string',
            'calibration' => 'nullable'
        ]);
        try {
            $validateData['user_id'] = auth()->user()->id;
            Category::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validateData = $request->validate([
            'category_id' => 'nullable',
            'letter' => 'nullable',
            'name' => 'required',
            'level' => 'required|string',
            'calibration' => 'nullable'
        ]);
        try {
            $category->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
