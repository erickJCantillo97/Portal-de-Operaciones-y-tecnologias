<?php

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

function getEmpleadosAPI(): mixed
{
    try {
        if (getToken()){
            $json =  Http::acceptJson()->withToken(session()->get('token'))
                    ->get(env('ROUTE_API').'/listado_personal_sap_activos_busqueda'
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
    $user = Http::acceptJson()->withToken(session()->get('token'))->get(env('ROUTE_API').'/user');

    if(!isset($user['id'])){
        return login();
    }

    return true;
}

function login(): bool{
    try{
        $login = Http::acceptJson()->post(env('ROUTE_API').'/auth/login',
            [
                'username' => 'portalreuniones',
                'password' => 'cG9ydGFscmV1bmlvbmVz',
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






