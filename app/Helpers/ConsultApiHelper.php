<?php

use App\Models\Labor;
use Illuminate\Support\Facades\Http;

define('ROUTE_API', 'https://servicioapi.cotecmar.com');



function getToken(): bool
{

    if (session()->get('token') == null) {
        return login();
    }
    $user = Http::acceptJson()->withToken(session()->get('token'))->get(ROUTE_API . '/user');

    if (!isset($user['id'])) {
        return login();
    }

    return true;
}

function login(): bool
{
    try {
        $login = Http::acceptJson()->post(
            ROUTE_API . '/auth/login',
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
        dd($e);
    }
}

function GDTipologias()
{
    try {
        if (getToken()) {
            $json = Http::acceptJson()->withToken(session()->get('token'))
                ->get(
                    ROUTE_API . '/trd_gd_view'
                )->json();

            return collect($json);
        }
        dd('Sin token');
    } catch (\Throwable $th) {
        dd($th);
    }
}
