<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(PermissionSeeder::class);

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

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
