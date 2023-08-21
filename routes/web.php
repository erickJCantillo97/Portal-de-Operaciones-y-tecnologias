<?php

use App\Models\ModelToolsAterior;
use App\Models\SWBS\BaseActivity;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'),'verified','verifyRol'])->group(function () {
    Route::get('/dashboard', function () {
        //return ModelToolsAterior::get();
        return Inertia::render('Dashboard');
    })->name('dashboard');



    Route::get('pruebaApi', function (){
        if(auth()->user()->hasRole('Super Admin')){
            return getEmpleadosAPI()->groupBy('GERENCIA');
        }
        return searchEmpleados('GERENCIA', auth()->user()->gerencia)->groupBy('OFICINA');
    })->name('pruebaApi');

    Route::get('simple/crud', function(Request $request){
        $request = $request->all();
    })->name('simple.crud');


    Route::get('get/gerencias', function(){

        return response()->json(['gerencias'=>gerencias()]);
    })->name('get.gerencias');




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


Route::get('recuperarDatos',function (){

    $datos = ModelToolsAterior::select('name', 'ord', 'validity', 'status')->get();
    foreach ($datos as $dato) {
        # code...
        BaseActivity::create(json_decode($dato, true));
    }
});
