<?php

use App\Models\Labor;
use Illuminate\Support\Facades\Http;

define('ROUTE_API', 'https://servicioapi.cotecmar.com');
function getEmpleadosAPI(): mixed
{
    try {
        if (getToken()){
            $json =  Http::acceptJson()->withToken(session()->get('token'))
                    ->get(ROUTE_API.'/listado_personal_cargo_costo_da_view'
                    )->json();
            return  collect($json);
        }
        dd('Sin token');

    } catch (\Throwable $th) {
        dd($th);
    }
}

function getToken(): bool
{

    if (session()->get('token') == null) {
        return login();
    }
    $user = Http::acceptJson()->withToken(session()->get('token'))->get(ROUTE_API.'/user');

    if (! isset($user['id'])) {
        return login();
    }

    return true;
}

function login(): bool
{
    try {
        $login = Http::acceptJson()->post(ROUTE_API.'/auth/login',
            [
                'username' => 'portalOperTecnologia',
                'password' => 'cG9ydGFsT3BlclRlY25vbG9naWE=',
            ]
        )->json();
        if ($login['status'] == 'true') {
            session()->put('token', $login['token']);

            return true;
        }

        return false;
    } catch (Exception $e) {
        return false;
    }
}

function searchEmpleados(string $clave, string $valor)
{

    return getEmpleadosAPI()->filter(function ($employee) use ($clave, $valor) {
        return $employee[$clave] == $valor;
    });
}

function pokeapi()
{
    HTTP::acceptJson()->get('https://pokeapi.co/api/v2/pokemon/');
}

function UpdateCargos() {
    try {
        if (getToken()){
            $json =  Http::acceptJson()->withToken(session()->get('token'))
                    ->get(ROUTE_API.'/listado_cargo_promedio_salarial_da_view'
                    )->json();
                    foreach ($json as $key => $cargo) {
                        Labor::firstOrCreate([
                            'name' => $cargo['Cargo'],
                            'gerencia' => $cargo['Gerencia'],
                            'w' => $cargo['Costo_Mes'],
                            'costo_dia' => $cargo['Costo_Dia'],
                            'costo_hora' => $cargo['Costo_Hora'],
                        ]);
                    }
            return  collect($json);
        }
        dd('Sin token');

    } catch (\Throwable $th) {
        dd($th);
    }

}

function getPersonalGerenciaOficina(string $gerencia = null, string $oficina = null ){
    $personal = [];
        if( $gerencia == null){
            $personal = getEmpleadosAPI();
        }else if( $gerencia != null && $oficina == null){
            $personal = searchEmpleados('Gerencia', $gerencia);
        }else if($gerencia != null && $oficina != null){
            $personal = searchEmpleados('Gerencia', $gerencia)->filter(function ($employee) use ($gerencia, $oficina) {
                return $employee['Gerencia'] == $gerencia && $employee['Oficina'] == $oficina;
            });;
        }

        return $personal;
}
