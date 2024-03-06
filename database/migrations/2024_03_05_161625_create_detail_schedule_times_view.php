<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        SELECT 
        employee_id 'idUsuario',schedules.name 'nombre', fecha, hora_inicio 'horaInicio',hora_fin 'horaFin', tasks.id 'idTask',
        tasks.name 'nombreTask',a.name 'nombrePadreTask', projects.name 'NombreProyecto'
        from 
        schedule_times   
        inner join schedules  on schedule_times.schedule_id = schedules.id and schedules.deleted_at is null
        inner join tasks on schedules.task_id = tasks.id and tasks.deleted_at is null
        inner join tasks a on tasks.task_id = a.id and a.deleted_at is null        
        inner join projects on tasks.project_id = projects.id and projects.deleted_at is null
        WHERE schedule_times.deleted_at is null 
        
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS detail_schedule_times;");
    }
};
