<?php

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

define('ROUTE_API' , "https://servicioapi.cotecmar.com");
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

function getToken(): bool{

    if(session()->get('token') == null)
    {
        return login();
    }
    $user = Http::acceptJson()->withToken(session()->get('token'))->get(ROUTE_API.'/user');

    if(!isset($user['id'])){
        return login();
    }

    return true;
}

function login(): bool{
    try{
        $login = Http::acceptJson()->post(ROUTE_API.'/auth/login',
            [
                'username' => 'portalOperTecnologia',
                'password' => 'cG9ydGFsT3BlclRlY25vbG9naWE=',
            ]
        )->json();
        if($login['status'] == 'true'){
            session()->put('token', $login['token']);
            return true;
        }
        return false;
    }catch(Exception $e){
        return false;
    }
}

function searchEmpleados(String $clave, String $valor)
{
    return getEmpleadosAPI()->filter(function ($employee) use ($clave, $valor){
            return $employee[$clave] == $valor;
    });
}

function pokeapi () {
    HTTP::acceptJson()->get('https://pokeapi.co/api/v2/pokemon/');
}

function getCargos() {
    try {
        if (getToken()){
            $json =  Http::acceptJson()->withToken(session()->get('token'))
                    ->get(ROUTE_API.'/listado_cargo_promedio_salarial_da_view'
                    )->json();
            return  collect($json);
        }
        dd('Sin token');

    } catch (\Throwable $th) {
        dd($th);
    }

}




