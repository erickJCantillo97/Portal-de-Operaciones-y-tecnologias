<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extended_schedules', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('start_hour');
            $table->string('end_hour');
            $table->string('description')->nullable();
            $table->bigInteger('project_id')->index()->unsigned();
            $table->bigInteger('task_id')->index()->unsigned();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extended_schedules');
    }
};
