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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id')->nullable()->index();
            $table->unsignedBigInteger('authorization_id')->nullable()->index();
            $table->unsignedBigInteger('quote_id')->nullable()->index();
            $table->unsignedBigInteger('ship_id')->index();
            $table->string('intern_communications')->nullable();
            $table->string('name'); //Este nombre debe ser calculado (Ship_YYYY_consecutivo)
            $table->string('gerencia');
            $table->date('start_date');
            $table->date('end_date');
            $table->float('hoursPerDay')->default(floatval(8.5));
            $table->integer('daysPerWeek')->default(5);
            $table->integer('daysPerMonth')->default(20);
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
