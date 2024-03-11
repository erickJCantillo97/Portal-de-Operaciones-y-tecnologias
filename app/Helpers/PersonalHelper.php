<?php

use App\Ldap\User;
use App\Models\Labor;
use App\Models\Personal\Employee;
use App\Models\Personal\Personal;
use App\Models\Personal\WorkingTeams;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use PhpParser\Node\Expr\Empty_;

function UpdateCargos()
{
    try {
        if (getToken()) {
            $json = Http::acceptJson()->withToken(session()->get('token'))
                ->get(
                    ROUTE_API . '/listado_cargo_promedio_salarial_da_view'
                )->json();
            foreach ($json as $key => $cargo) {
                Labor::firstOrCreate([
                    'name' => $cargo['Cargo'],
                    'gerencia' => $cargo['Gerencia'],
                    'costo_mes' => $cargo['Costo_Mes'],
                    'costo_dia' => $cargo['Costo_Dia'],
                    'costo_hora' => $cargo['Costo_Hora'],
                ]);
            }

            return collect($json);
        }
        dd('Sin token');
    } catch (\Throwable $th) {
        dd($th);
    }
}

function getPersonalGerenciaOficina(string $gerencia = null, string $oficina = null)
{
    $personal = [];
    if ($gerencia == null) {
        $personal = getEmpleadosAPI();
    } elseif ($gerencia != null && $oficina == null) {
        $personal = searchEmpleados('Gerencia', $gerencia);
    } elseif ($gerencia != null && $oficina != null) {
        $personal = searchEmpleados('Gerencia', $gerencia)->filter(function ($employee) use ($gerencia, $oficina) {
            return $employee['Gerencia'] == $gerencia && $employee['Oficina'] == $oficina;
        });
    }

    return $personal;
}

function getEmpleadosAPI(): mixed
{
    return Employee::get();
}
function setEmpleadosAPI(): mixed
{
    Employee::truncate();
    try {
        if (getToken()) {
            $json = Http::acceptJson()->withToken(session()->get('token'))
                ->get(
                    ROUTE_API . '/listado_personal_cargo_costo_da_view'
                )->json();

            foreach (collect($json) as $employee) {
                // if (!isset($employee['JI_Num_SAP']))
                //     dd($employee);
                if (isset($employee))
                    Employee::create((array) $employee);
            };
        }
        dd('Sin token');
    } catch (\Throwable $th) {
        dd($th);
    }
}

function searchEmpleados(string $clave, string $valor)
{
    return getEmpleadosAPI()->filter(function ($employee) use ($clave, $valor) {
        return strpos($employee[$clave], $valor) !== false;
    });
}
// ->map(function ($person) {
//     return [
//         'Num_SAP' => (int) $person['Num_SAP'],
//         'Fecha_Final' => Carbon::createFromFormat('Ymd', $person['Fecha_Final']),
//         'Costo_Hora' => $person['Costo_Hora'],
//         'Costo_Mes' => $person['Costo_Mes'],
//         'Oficina' => $person['Oficina'],
//         'canDelete' => $person['JI_Num_SAP'] != auth()->user()->num_sap,
//         'Nombres_Apellidos' => $person['Nombres_Apellidos'],
//         'Cargo' => $person['Cargo'],
//         'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
//     ];
// });


function getPersonalUser()
{
    $NumSAPPersonal = Personal::where('boss_id', auth()->user()->num_sap)->pluck('user_id')->map(function ($p) {
        return
            str_pad(($p), 8, '0', STR_PAD_LEFT);
    })->toArray();

    $miPersonal = Employee::where('JI_Num_SAP', auth()->user()->num_sap)->orWhereIn('Num_SAP', $NumSAPPersonal)->get()->map(function ($person) {
        return [
            'Num_SAP' => (int) $person['Num_SAP'],
            'Fecha_Final' => Carbon::createFromFormat('Ymd', $person['Fecha_Final']),
            'Costo_Hora' => $person['Costo_Hora'],
            'Costo_Mes' => $person['Costo_Mes'],
            'Oficina' => $person['Oficina'],
            'canDelete' => $person['JI_Num_SAP'] != auth()->user()->num_sap,
            'Nombres_Apellidos' => $person['Nombres_Apellidos'],
            'Cargo' => $person['Cargo'],
            'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
        ];
    });
    return $miPersonal;
}

function getPersonalGroup($grupo_id)
{
    $users_SAP = WorkingTeams::where('team_id', $grupo_id)->pluck('user_num_sap')->toArray();
    return getPersonalUser()->filter(function ($persona) use ($users_SAP) {
        return in_array($persona['Num_SAP'], $users_SAP);
    });
}
