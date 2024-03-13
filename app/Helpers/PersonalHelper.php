<?php

use App\Ldap\User;
use App\Models\Labor;
use App\Models\Personal\Employee;
use App\Models\Personal\Personal;
use App\Models\Personal\WorkingTeams;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use PhpParser\Node\Expr\Empty_;
use Spatie\Holidays\Holidays;

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
        $personal = Employee::where('Gerencia', $gerencia)->where('Oficina', $oficina)->get();
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
        return Employee::get();
        // dd('Sin token');
    } catch (\Throwable $th) {
        dd($th);
    }
}

function searchEmpleados(string $clave, string $valor)
{
    return Employee::where($clave, $valor)->get();
}

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


/*
esta función valida que la fecha enviada $date sea una fecha valida, 
excluyendo o incluyendo los fines de semana y feriados
*/
function getWorkingDays(string $date, int $Workingdays = 5)
{
    /* Preguntamos cuando dias de trabajos habiles se quiere usar.
       Si $workingdays = 5, quiere decir que se trabaja de lunes a viernes
       Si $workingdays = 6, quiere decir que se trabaja de lunes a sabados
    */
    $validDate = [];
    switch ($Workingdays){
        case 5:
            array_push($validDate,'Saturday', 'Sunday');
            break;
        case 6:
            array_push($validDate,'Sunday');
            break;
        default:
            array_push($validDate,'');
            break;
        }
        //obtenemos el nombre del dia
        $dayName = Carbon::parse($date)->format('l');

        //validamos que la fecha seleccionada no sea fin de semana
        if(in_array($dayName, $validDate)){
            return false;
        }else{
            //validamos que la fecha seleccionada no sea dia feriado
            if(Holidays::for('co')->isHoliday($date)){
                return false;
        }
        //si llegamos a este punto, quiere decir que la fecha no es fin de semana ni es feriado.
        return true;
        }

}
