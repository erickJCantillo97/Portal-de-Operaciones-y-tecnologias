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
        Schema::create('calendar_intervals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('calendar_id')->index()->unsigned();
            $table->boolean('isWorking')->default(false);
            $table->integer('priority');
            $table->string('recurrentStartDate')->nullable();
            $table->string('recurrentEndDate')->nullable();
            $table->dateTime('startDate')->nullable();
            $table->dateTime('endDate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_intervals');
    }
};
