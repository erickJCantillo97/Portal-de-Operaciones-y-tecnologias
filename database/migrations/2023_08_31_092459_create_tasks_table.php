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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('authorization_id')->index()->nullable();
            $table->unsignedBigInteger('task_id')->index()->nullable();
            $table->unsignedBigInteger('project_id')->index()->nullable();
            $table->string('manager')->nullable();
            $table->boolean('manuallyScheduled')->default(1);
            $table->string('executor')->nullable();
            $table->string('name');
            $table->string('durationUnit');
            $table->double('percentDone');
            $table->double('duration');
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
