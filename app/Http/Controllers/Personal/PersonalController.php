<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Ldap\User;
use App\Models\Labor;
use App\Models\Personal\Personal;
use App\Models\Personal\Team;
use App\Models\Personal\WorkingTeams;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $groups = Team::where('user_id', auth()->user()->id)->orderBy('name')->get();

        if ($request->id) {
            $group = Team::find($request->id);
            return inertia('Personal/Index', [
                'miPersonal' => getPersonalGroup($request->id),
                'group' =>  $group,
                'groups' =>   $groups
            ]);
        }
        return inertia('Personal/Index', [
            'miPersonal' => getPersonalUser(),
            'groups' =>   $groups
        ]);
    }
    /* Esta funcion devulve el persona a cargo del usuario logeado o las personas del grupo pasado por parametros */
    public function getPersonalUser($id = null)
    {
        return response()->json([
            'personal' => isset($id) ? getPersonalGroup($id) : getPersonalUser(),
        ]);
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
                $persona->gerencia_lent = auth()->user()->gerencia;
                $persona->oficina_lent = auth()->user()->oficina;
                $persona->boss_id = auth()->user()->num_sap;
                $persona->return_date = Carbon::parse($validateData['fecha_devolucion'])->format('Y-m-d') ?? null;
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
    public function destroy($personal)
    {
        try {
            $persona = Personal::whereUserId($personal)->first();
            if ($persona->boss_last_id != 0) {
                $persona->boss_last_id = null;
                $persona->boss_id = $persona->boss_last_id ?? 0;
                $persona->save();
            } else {
                $persona->delete();
            }

            return back()->with(['message' => 'Personal Eliminado correctamente'], 200);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error Al eliminar : ' . $e);
        }
    }

    /* Con esta  función podemos listar los usuario que estan activos en el DA */
    public function getPersonalActivo(Request $request)
    {
        //Validar para usuario de tipo administrador, puedan ver todo el personal cotecmar
        $personal = getPersonalGerenciaOficina()->values()->map(function ($person) {
            return [
                'Num_SAP' => (int) $person['Num_SAP'],
                'Fecha_Final' => $person['Fecha_Final'],
                'Costo_Hora' => $person['Costo_Hora'],
                'Correo' => $person['Correo'],
                'Costo_Mes' => $person['Costo_Mes'],
                'Oficina' => $person['Oficina'],
                'Nombres_Apellidos' => $person['Nombres_Apellidos'],
                'Cargo' => $person['Cargo'],
                // 'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
            ];
        });
        if ($request->expectsJson()) {
            return response()->json([
                'personal' => $personal
            ]);
        }

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
