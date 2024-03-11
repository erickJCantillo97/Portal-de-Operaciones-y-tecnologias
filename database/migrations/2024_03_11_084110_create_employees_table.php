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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('Num_SAP')->unique();
            $table->string('Identificacion');
            $table->string('Nombres_Apellidos');
            $table->string('Cargo');
            $table->string('Gerencia');
            $table->string('Oficina');
            $table->string('Usuario');
            $table->string('Correo');
            $table->string('Tipo');
            $table->string('Costo_Mes');
            $table->string('Costo_Dia');
            $table->string('Costo_Hora');
            $table->string('Fecha_Inicio');
            $table->string('Fecha_Final');
            $table->string('JI_Num_SAP')->nullable();
            $table->string('JI_Nombres_Apellidos')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
