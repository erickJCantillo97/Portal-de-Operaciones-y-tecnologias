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
        Schema::create('grafos', function (Blueprint $table) {
            $table->bigInteger('project_id')->unsigned()->index()->nullable();
            $table->bigInteger('pep_id')->unsigned()->index()->nullable();
            $table->string('identification')->nullable();
            $table->string('grafo_id')->nullable();
            $table->double('materials')->default(0);
            $table->double('labor')->default(0);
            $table->double('services')->default(0);
            $table->double('materials_ejecutados')->default(0);
            $table->double('labor_ejecutados')->default(0);
            $table->double('services_ejecutados')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grafos', function (Blueprint $table) {
            //
        });
    }
};
