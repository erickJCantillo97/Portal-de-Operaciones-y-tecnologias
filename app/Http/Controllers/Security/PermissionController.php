<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData = $request->validate(
            [
                'name'=>'required|unique:permissions,name',
            ]
        );
        $validateData['guard_name'] = 'web';
        $roles =  collect($request->roles)->map(function ($permiso) {
            return $permiso['name'];
        });
        try{

            $permiso = Permission::create($validateData);

            foreach ($roles as $role) {
                $r = Role::where('name', $role)->first();
                $r->givePermissionTo($permiso);
            }
        }catch (Exception $e) {
            return back()->withErrors('message' , 'Ocurrio un Error: '.$e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        try{
            $permission->delete();
        }catch(Exception $e){
            return back()->withErrors('message', 'Ocurrio un Error: '.$e);
        }
    }
}
