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

        User::create([
            'name' => 'Giovany Enrique Buelvas Jaspe',
            'username' => 'gbuelvas',
            'identificacion' => 1047229183,
            'password' => '$2y$10$faXMcfVndQHzJLKIM0mUc.Oiz/FPuqxLkbMkd1kYchRaKmB1jupui',
            'guid' => '972be66b-708e-4556-8bc3-2e750f334f10',
        ])->assignRole('Super Admin');;
        User::create([
            'name' => 'Erik Jose Cantillo Jimenez',
            'username' => 'ecantillo',
            'identificacion' => 1047496054,
            'password' => '$2y$10$Cac1FjtkYdIZQ8C71aruHus.p7GDJVo1f9qUOKlRaGQ0DLiCXDXge',
            'guid' => 'b3a6ddfe-aec9-42d7-b863-3147ccfe7b94',
        ])->assignRole('Super Admin');;
        User::create([
            'name' => 'Ronny Gutierrez Vitola',
            'username' => 'rgutierrez',
            'identificacion' => 1143359697,
            'password' => '$2y$10$U9tlAaKzSr74yctVCCNqE.B7NpzNPRvC8Tb7SEfjewY7SCDdOA7RK',
            'guid' => '82b3de25-2e73-4c2d-96bc-47162a1d9626',
        ])->assignRole('Super Admin');;

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

            ['name' => 'dashboard projects view', 'guard_name' => 'web'],
        ];

        foreach ($permisos as $key => $permiso) {
            # code...
            $p = Permission::create($permiso);
            $role = Role::where('name', 'Super Admin')->first();
            $role->givePermissionTo($p['name']);
        }
    }
}
