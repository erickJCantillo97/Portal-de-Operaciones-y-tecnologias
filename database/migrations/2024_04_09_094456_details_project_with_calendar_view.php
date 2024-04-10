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
        CREATE or ALTER VIEW detail_project_with_calendars
        AS
        select project_id, calendars.name, calendar_intervals.id 'idCalendarInterval',
        calendar_intervals.calendar_id 'calendarId', calendar_intervals.isWorking,
        calendar_intervals.priority, calendar_intervals.recurrentStartDate,
        calendar_intervals.recurrentEndDate, calendar_intervals.startDate,
        calendar_intervals.endDate
        from calendars
        inner join calendar_intervals on calendar_id = calendars.id and calendar_intervals.deleted_at is null
		inner join project_with_calendars on project_with_calendars.calendar_id = calendars.id and project_with_calendars.deleted_at is null
        where calendars.deleted_at is null
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS details_project_with_calendar;");
    }
};
