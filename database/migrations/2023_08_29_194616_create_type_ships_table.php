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
        Schema::create('type_ships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('disinger')->nullable();
            $table->enum('hull_material', ['ACERO', 'ALUMINIO', 'MATERIALES COMPUESTOS'])->nullable(); //material del casco
            $table->double('length')->nullable(); //eslra
            $table->double('breadth')->nullable(); //Manga
            $table->double('draught')->nullable(); //calado de diseño
            $table->double('depth')->nullable(); //punta
            $table->double('full_load')->nullable();
            $table->double('light_ship')->nullable();
            $table->double('power_total')->nullable();
            $table->string('propulsion_type')->nullable();
            $table->string('velocity')->nullable();
            $table->double('autonomias')->nullable();
            $table->double('autonomy')->nullable();
            $table->double('crew')->nullable();
            $table->double('GT')->nullable();
            $table->double('CGT')->nullable();
            $table->double('bollard_pull')->nullable();
            $table->string('clasification')->nullable();
            $table->string('render')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_ships');
    }
};
