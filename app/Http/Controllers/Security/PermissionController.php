<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles', 'roles.permissions')->orderBy('gerencia')->get()->map(function ($user) {
            $roles = collect($user['roles'])->pluck('name')->toArray();

            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'photo' => $user['photo'],
                'cargo' => $user['cargo'],
                'gerencia' => $user['gerencia'],
                'roles' => count($roles) == 0 ? ["Sin rol"] :  collect($roles)->map(function ($role) {
                    return $role;
                }),
                'rolesObj' => collect($user['roles'])->map(function ($role) {
                    return [
                        'id' => $role['id'],
                        'name' => $role['name']
                    ];
                }),
            ];
        });

        $roles = Role::with('permissions')->get()->map(function ($r) {
            return [
                'id' => $r['id'],
                'name' =>  $r['name'],
                'description' => $r['description'],
                'users' => User::role($r['name'])->get(),
                'permissions' => $r['permissions']
            ];
        });
        $permisos = Permission::orderBy('name')->get();

        return Inertia::render('Security/Index', [
            'users' => $users, 'roles' => $roles, 'permisos' => $permisos
        ]);
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
                'name' => 'required|unique:permissions,name',
            ]
        );
        $validateData['guard_name'] = 'web';
        $roles =  collect($request->roles)->map(function ($permiso) {
            return $permiso['name'];
        });
        try {

            $permiso = Permission::create($validateData);
            $r = Role::first();
            $r->givePermissionTo($permiso);
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
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
        try {
            $permission->delete();
        } catch (Exception $e) {
            return back()->withErrors('message', 'Ocurrio un Error: ' . $e);
        }
    }
}
