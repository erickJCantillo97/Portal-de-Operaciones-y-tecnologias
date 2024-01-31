<?php

namespace App\Http\Controllers\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\WareHouse\Category;
use App\Models\WareHouse\Tool;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tools = Tool::with('category','category.padre', 'category.padre.padre')->get();
        $categories = Category::has('padre')->with('padre', 'padre.padre')->where('level', 'Descripcion')
            ->get();
        return Inertia::render('WareHouse/Tools', [
            'tools' => $tools,
            'categories' => $categories,
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
            'category_id' => 'required',
            'serial' => 'required|unique:tools,serial',
            'SAP_code' => 'nullable|string',
            'value' => 'required',
            'brand' => 'nullable',
            'entry_date' => 'required|date',
            'is_small' => 'nullable',
            'description' => 'nullable|string',
            'imagen' => 'nullable'
        ]);

        try {
            $validateData['gerencia'] = auth()->user()->gerencia;
            $validateData['responsible_id'] = auth()->user()->id;
            $validateData['code'] = $this->createCode($validateData);
            $validateData['is_small'] = 0;
            // $validateData['criticidad'] = 0;
            Tool::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }


    protected function createCode($data)
    {
        $categoria = Category::where('id', $data['category_id'])->first();
        $equipo = Tool::whereHas('category.padre', function (Builder $query) use ($categoria) {
            $query->where('id', $categoria->padre->id);
        })->where("is_small", 0)->latest()->first();

        $valor = isset($equipo->code) ? substr($equipo->code, -3, 3) : 0;

        return $categoria->padre->padre->letter . "" . $categoria->padre->letter . "" . date_format(new DateTime($data['entry_date']), "y") . "01" . str_pad(($valor + 1), 3, '0', STR_PAD_LEFT);
    }


    /**
     * Display the specified resource.
     */
    public function show(Tool $tool)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tool $tool)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $tool->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tool)
    {
        try {
            $tool->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }
}
