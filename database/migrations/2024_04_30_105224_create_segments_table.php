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
        Schema::create('segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->index()->nullable();
            $table->boolean('allDay')->nullable();
            $table->unsignedBigInteger('calendar_id')->index()->nullable();
            $table->string('cls')->nullable();
            $table->string('direction')->nullable();
            $table->float('duration')->nullable();
            $table->string('durationUnit')->nullable();
            $table->string('endDate')->nullable();
            $table->boolean('manuallyScheduled')->nullable();
            $table->string('name')->nullable();
            $table->string('startDate')->nullable();
            $table->boolean('unscheduled')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segments');
    }
};
