<?php

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

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

    Route::get('settings/basic', function (){
        return Inertia::render('Settings/Basic/Index');
    })->name('settings.basic');

    Route::get('pruebaApi', function (){
        return getEmpleadosAPI()->groupBy('GERENCIA');
    })->name('pruebaApi');
    Route::get('simple/crud', function(Request $request){
        return $request;
    })->name('simple.crud');

    Route::get('get/gerencias', function(){
        $gerencias = array_map(function ($object) {
            $object->nombre = $object->name;
            unset($object->name);
            return $object;
        }, gerencias());
        return response()->json(['gerencias'=>$gerencias]);
    })->name('get.gerencias');

    Route::get('seguridad',  function (Request $request){
        $users = User::orderBy('gerencia')->get();
        return Inertia::render('Security/Index',[
            'users' => $users
        ]);
    })->name('security');


});
// Route::get('chagePassword', function (){
//     // Obtén el usuario autenticado (por ejemplo, usando Auth::user())
//     $authenticatedUser = Auth::user();

//     // Recupera el modelo LDAP del usuario autenticado
//     $ldapUser = \LdapRecord\Models\ActiveDirectory\User::where('samaccountname', 'ecantillo')->first();
//     // Guarda los cambios en el servidor LDAP
//     $ldapUser->password = "Agosto2022";
//     $ldapUser->save();
//     return $ldapUser;
// });

