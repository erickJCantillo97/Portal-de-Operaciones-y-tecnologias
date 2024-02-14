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
        Schema::create('adavnce_project_weeks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->index()->unsigned();
            $table->string('week');
            $table->date('date')->nullable();
            $table->double('planned_progress');
            $table->double('real_progress')->nullable();
            $table->double('CPI')->nullable();
            $table->double('SPI')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adavnce_project_weeks');
    }
};
