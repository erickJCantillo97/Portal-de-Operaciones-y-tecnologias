<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User as LdapUser;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::get('pruebaApi', function (){
   return getEmpleadosAPI()->groupBy('GERENCIA');
})->name('pruebaApi');


Route::get('chagePassword', function (){
    // Obtén el usuario autenticado (por ejemplo, usando Auth::user())
    $authenticatedUser = Auth::user();

    // Recupera el modelo LDAP del usuario autenticado
    $ldapUser = \LdapRecord\Models\ActiveDirectory\User::where('samaccountname', 'ecantillo')->first();
    // Guarda los cambios en el servidor LDAP
    $ldapUser->password = "Agosto2022";
    $ldapUser->save();
    return $ldapUser;
});
