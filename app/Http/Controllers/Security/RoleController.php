<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
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
                'name' => 'required|unique:roles,name',
                'description' => 'required',
            ]
        );
        try {
            $validateData['guard_name'] = 'web';
            $permisos =  collect($request->permissions)->map(function ($permiso) {
                return $permiso['name'];
            });
            $role = Role::create($validateData);
            $role->syncPermissions($permisos);
        } catch (\Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
        }
    }

    public function assignmentRoleToUser($role, User $user)
    {
        try {
            $user->assignRole($role);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
        }
    }

    public function removeRoleToUser($role, User $user)
    {
        try {
            $user->removeRole($role);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validateData = $request->validate(
            [
                'name' => 'required|unique:roles,name,'.$role->id,
                'description' => 'required',
            ]
        );
        try {
            $validateData['guard_name'] = 'web';
            $permisos =  collect($request->permissions)->map(function ($permiso) {
                return $permiso['name'];
            });
            $role->update($validateData);
            $role->syncPermissions($permisos);
        } catch (\Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
        }
    }
}
