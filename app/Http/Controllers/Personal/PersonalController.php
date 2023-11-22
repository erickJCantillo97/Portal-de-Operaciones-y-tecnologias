<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Models\Labor;
use App\Models\Personal\Personal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personal = searchEmpleados('JI_Num_SAP', '00000261')->values(); //Se debe cambiar el num Sap por el del usuario logueado
        return inertia('Personal/Index', ['personal' => $personal]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([]);
        try {
            Personal::create($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Crear : ' . $e);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Personal $personal)
    {
        $validateData = $request->validate([
            //
        ]);

        try {
            $personal->update($validateData);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al Actualizar : ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personal $personal)
    {
        try {
            $personal->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }


    /* Con esta  función podemos listar los usuario que estan activos en el DA */
    public function getPersonalActivo()
    {
        //Validar para usuario de tipo administrador, puedan ver todo el personal cotecmar
        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia)->values()->map(function ($person) {
            return [
                'Num_SAP' => (int) $person['Num_SAP'],
                'Fecha_Final' => $person['Fecha_Final'],
                'Costo_Hora' => $person['Costo_Hora'],
                'Costo_Mes' => $person['Costo_Mes'],
                'Oficina' => $person['Oficina'],
                'Nombres_Apellidos' => $person['Nombres_Apellidos'],
                'Cargo' => $person['Cargo'],
                'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
            ];
        });

        return inertia('Personal/Activos', [
            'personal' => $personal,
        ]);
    }


    public function getPersonalCargo()
    {
        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia)->groupBy('Cargo');
        return response()->json([
            'personal' => $personal,
        ]);
    }

    public function getCargos()
    {
        $cargos = Labor::get()->map(function ($cargo) {
            return [
                'id' => $cargo->id,
                'name' => $cargo->name,
                'costo_hora' => '$ ' . number_format($cargo->costo_hora, 0),
            ];
        });

        return response()->json([
            'cargos' => $cargos,
        ]);
    }

    /*
        Esta funcion permite devolver las personas de una oficiana
        permite buscarlas con el request de gerencia y oficina
        o devolverá el personal de la Oficina de la persona logueada
    */
    public function getPersonalGerenicaOcifina(Request $request)
    {
        $gerencia = $request->gerencia ?? auth()->user()->gerencia;

        $oficina = $request->oficina ?? auth()->user()->oficina;

        $personal = getPersonalGerenciaOficina($gerencia, $oficina)->map(function ($person) {
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

    public function searchPersonal(Request $request)
    {
        $key = $request->key;
        $value = $request->value;
        if (!isset($request->all)) {
            return response()->json([
                'employees' => searchEmpleados($key, $value)->filter(function ($employee) { //TODO Cambiar el all() por filter()
                    return $employee['Gerencia'] == Auth::user()->gerencia;
                }),
            ])->values()->map(function ($person) {
                return [
                    'id' => (int) $person['Num_SAP'],
                    'Fecha_Final' => $person['Fecha_Final'],
                    'Costo_Hora' => $person['Costo_Hora'],
                    'Costo_Mes' => $person['Costo_Mes'],
                    'Oficina' => $person['Oficina'],
                    'name' => $person['Nombres_Apellidos'],
                    'Cargo' => $person['Cargo'],
                    'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
                ];
            });
        }

        return response()->json([
            'employees' => searchEmpleados($key, $value)->values()->map(function ($person) {
                return [
                    'id' => (int) $person['Num_SAP'],
                    'Fecha_Final' => $person['Fecha_Final'],
                    'Costo_Hora' => $person['Costo_Hora'],
                    'Costo_Mes' => $person['Costo_Mes'],
                    'Oficina' => $person['Oficina'],
                    'name' => $person['Nombres_Apellidos'],
                    'Cargo' => $person['Cargo'],
                    'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
                ];
            }),
        ]);
    }
}
