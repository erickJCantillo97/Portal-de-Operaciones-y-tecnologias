<?php

use App\Ldap\User;
use App\Models\Labor;
use App\Models\Personal\Personal;
use App\Models\Personal\WorkingTeams;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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
    try {
        if (getToken()) {
            $json = Http::acceptJson()->withToken(session()->get('token'))
                ->get(
                    ROUTE_API . '/listado_personal_cargo_costo_da_view'
                )->json();

            return collect($json);
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


function getPersonalUser()
{
    $NumSAPPersonal = Personal::where('boss_id', auth()->user()->num_sap)->pluck('user_id')->toArray();

    $miPersonal = getEmpleadosAPI()->filter(function ($employee) use ($NumSAPPersonal) {
        return $employee['JI_Num_SAP'] == auth()->user()->num_sap || in_array($employee['Num_SAP'], $NumSAPPersonal);
    })->values()->map(function ($person) use ($NumSAPPersonal) {
        return [
            'Num_SAP' => (int) $person['Num_SAP'],
            'Fecha_Final' => Carbon::createFromFormat('Ymd', $person['Fecha_Final']),
            'Costo_Hora' => $person['Costo_Hora'],
            'Costo_Mes' => $person['Costo_Mes'],
            'Oficina' => $person['Oficina'],
            'canDelete' => in_array($person['Num_SAP'], $NumSAPPersonal) && $person['JI_Num_SAP'] != auth()->user()->num_sap,
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
