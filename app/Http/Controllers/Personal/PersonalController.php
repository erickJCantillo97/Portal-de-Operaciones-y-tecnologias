<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Models\Labor;
use Illuminate\Http\Request;

class PersonalController extends Controller
{

    public function getPersonalCargo(){

        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia)->groupBy('Cargo');

        return response()->json([
            'personal' => $personal,
        ]);
    }

    public function getCargos(){
       $cargos  = Labor::get()->map(function ($cargo) {
            return [
                'id' => $cargo->id,
                'name' => $cargo->name,
                'costo_hora' => '$ '.number_format($cargo->costo_hora, 0),
            ];
        });
        return response()->json([
            'cargos' => $cargos,
        ]);
    }

    public function getPersonal(){
        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia, auth()->user()->oficina)->map(function ($person)  {
            return [
                'Num_SAP' => (int) $person['Num_SAP'],
                'Correo' => $person['Correo'],
                'Nombres_Apellidos' => $person['Nombres_Apellidos'],
                'Cargo' => $person['Cargo'],
                'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
            ];
        });

        return response()->json([
            'personal' => $personal,
        ]);
    }
}
