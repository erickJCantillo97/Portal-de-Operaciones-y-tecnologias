<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role::create(['name' =>'Super Admin']);
        // Role::create(['name' =>'Administrador']);
        // Role::create(['name' =>'Visitante']);
        // Role::create(['name' =>'Cliente']);

        User::where('username', 'rgutierrez')->first()->assignRole('Super Admin');
    }
}
