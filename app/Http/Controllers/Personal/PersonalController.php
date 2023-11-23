<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Models\Labor;
use App\Models\Personal\Personal;
use Carbon\Carbon;
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
        $NumSAPPersonal = Personal::where('boss_id', auth()->user()->id)->pluck('user_id')->toArray();

        $miPersonal = getEmpleadosAPI()->filter(function ($employee) use ($NumSAPPersonal) {
            return $employee['JI_Num_SAP'] == auth()->user()->id || in_array($employee['Num_SAP'], $NumSAPPersonal);
        })->values()->map(function ($person) use ($NumSAPPersonal) {
            return [
                'Num_SAP' => (int) $person['Num_SAP'],
                'Fecha_Final' => $person['Fecha_Final'],
                'Costo_Hora' => $person['Costo_Hora'],
                'Costo_Mes' => $person['Costo_Mes'],
                'Oficina' => $person['Oficina'],
                'canDelete' => in_array($person['Num_SAP'], $NumSAPPersonal) && $person['JI_Num_SAP'] != auth()->user()->id,
                'Nombres_Apellidos' => $person['Nombres_Apellidos'],
                'Cargo' => $person['Cargo'],
                'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
            ];
        }); //Se debe cambiar el num Sap por el del usuario logueado


        $personal = getPersonalGerenciaOficina(auth()->user()->gerencia)->values()->map(function ($person) {
            return [
                'Num_SAP' => (int) $person['Num_SAP'],
                'Nombres_Apellidos' => $person['Nombres_Apellidos'],
                'Oficina' => $person['Oficina'],
                'Cargo' => $person['Cargo'],
                'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
            ];
        }); //Se debe cambiar el num Sap por el del usuario logueado;

        return inertia('Personal/Index', ['miPersonal' => $miPersonal, 'personal' => $personal]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'users' => 'required|array|distinct',
            'users.*.Num_SAP' => 'required',
            'fecha_devolucion' => 'nullable'
        ]);

        try {
            foreach ($validateData['users'] as $user) {
                $persona = Personal::firstOrNew([
                    'user_id' => $user['Num_SAP'],
                ]);
                $persona->boss_last_id = $persona->boss_id ?? null;
                $persona->boss_id = auth()->user()->id;
                $persona->return_date =  Carbon::parse($validateData['fecha_devolucion'])->format('Y-m-d') ?? null;
                $persona->save();
            }
            return back()->with(['message' => 'Personal Añadido correctamente'], 200);
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
