<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Administrador']);

        User::where('username', 'gbuelvas')->first()->assignRole('Super Admin');
        User::where('username', 'ecantillo')->first()->assignRole('Super Admin');
        User::where('username', 'rgutierrez')->first()->assignRole('Super Admin');

        $permisos = [
            // Permisos para funciones de la APP
            //categorias
            ['name' => 'category create', 'guard_name' => 'web'],
            ['name' => 'category read', 'guard_name' => 'web'],
            ['name' => 'category edit', 'guard_name' => 'web'],
            ['name' => 'category delete', 'guard_name' => 'web'],

            //equipos - Herramientas
            ['name' => 'tool create', 'guard_name' => 'web'],
            ['name' => 'tool read', 'guard_name' => 'web'],
            ['name' => 'tool edit', 'guard_name' => 'web'],
            ['name' => 'tool delete', 'guard_name' => 'web'],

            //Asignaciones (Prestamos)
            ['name' => 'assignmentTool create', 'guard_name' => 'web'],
            ['name' => 'assignmentTool read', 'guard_name' => 'web'],
            ['name' => 'assignmentTool edit', 'guard_name' => 'web'],
            ['name' => 'assignmentTool delete', 'guard_name' => 'web'],

            //projects
            ['name' => 'projects create', 'guard_name' => 'web'],
            ['name' => 'projects read', 'guard_name' => 'web'],
            ['name' => 'projects edit', 'guard_name' => 'web'],
            ['name' => 'projects delete', 'guard_name' => 'web'],

            //Quotes
            ['name' => 'quote create', 'guard_name' => 'web'],
            ['name' => 'quote read', 'guard_name' => 'web'],
            ['name' => 'quote edit', 'guard_name' => 'web'],
            ['name' => 'quote delete', 'guard_name' => 'web'],

            //Programacion
            ['name' => 'programming create', 'guard_name' => 'web'],
            ['name' => 'programming read', 'guard_name' => 'web'],
            ['name' => 'programming edit', 'guard_name' => 'web'],
            ['name' => 'programming delete', 'guard_name' => 'web'],

            //customers
            ['name' => 'customer create', 'guard_name' => 'web'],
            ['name' => 'customer read', 'guard_name' => 'web'],
            ['name' => 'customer edit', 'guard_name' => 'web'],
            ['name' => 'customer delete', 'guard_name' => 'web'],

            //Contracts
            ['name' => 'contract create', 'guard_name' => 'web'],
            ['name' => 'contract read', 'guard_name' => 'web'],
            ['name' => 'contract edit', 'guard_name' => 'web'],
            ['name' => 'contract delete', 'guard_name' => 'web'],

            //Ships
            ['name' => 'ship create', 'guard_name' => 'web'],
            ['name' => 'ship read', 'guard_name' => 'web'],
            ['name' => 'ship edit', 'guard_name' => 'web'],
            ['name' => 'ship delete', 'guard_name' => 'web'],

            //Type Ships
            ['name' => 'typeShip create', 'guard_name' => 'web'],
            ['name' => 'typeShip read', 'guard_name' => 'web'],
            ['name' => 'typeShip edit', 'guard_name' => 'web'],
            ['name' => 'typeShip delete', 'guard_name' => 'web'],

            //sugerencias
            ['name' => 'suggestion create', 'guard_name' => 'web'],
            ['name' => 'suggestion read', 'guard_name' => 'web'],
            ['name' => 'Suggestion edit', 'guard_name' => 'web'],
            ['name' => 'suggestion delete', 'guard_name' => 'web'],
        ];

        foreach ($permisos as $key => $permiso) {
            # code...
            $p = Permission::create($permiso);
            $role = Role::where('name', 'Super Admin')->first();
            $role->syncPermissions($p);
        }
    }
}
