<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PersonalController extends Controller
{


    public function getPersonalCargo(){

        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia)->groupBy('Cargo');

        return response()->json([
            'personal' => $personal,
        ]);
    }
}
