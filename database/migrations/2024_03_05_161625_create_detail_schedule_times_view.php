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
        CREATE or alter VIEW [dbo].[detail_schedule_times]
        AS
        SELECT dbo.schedule_times.id AS idScheduleTime, dbo.schedules.id AS idSchedule, dbo.schedules.employee_id AS idUsuario, dbo.schedules.name AS nombre, dbo.schedules.fecha, dbo.schedule_times.hora_inicio AS horaInicio, dbo.schedule_times.hora_fin AS horaFin, 
                     dbo.tasks.id AS idTask, dbo.tasks.name AS nombreTask, a.name AS nombrePadreTask, dbo.projects.name AS NombreProyecto, dbo.schedules.deleted_at, dbo.tasks.percentDone
        FROM   dbo.schedule_times INNER JOIN
                     dbo.schedules ON dbo.schedule_times.schedule_id = dbo.schedules.id AND dbo.schedules.deleted_at IS NULL INNER JOIN
                     dbo.tasks ON dbo.schedules.task_id = dbo.tasks.id AND dbo.tasks.deleted_at IS NULL INNER JOIN
                     dbo.tasks AS a ON dbo.tasks.task_id = a.id AND a.deleted_at IS NULL INNER JOIN
                     dbo.projects ON dbo.tasks.project_id = dbo.projects.id AND dbo.projects.deleted_at IS NULL
        WHERE (dbo.schedule_times.deleted_at IS NULL) AND (dbo.schedules.deleted_at IS NULL) AND (dbo.tasks.percentDone < 100) AND (dbo.tasks.deleted_at IS NULL)
        
          go
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
