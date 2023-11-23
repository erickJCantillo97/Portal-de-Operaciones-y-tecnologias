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
            $table->string('name')->unique();
            $table->unsignedBigInteger('contract_id')->nullable()->index();
            $table->unsignedBigInteger('authorization_id')->nullable()->index();
            $table->unsignedBigInteger('quote_id')->nullable()->index();
            $table->enum('type',
                [
                    'PROYECTO DE VENTA (ARTEFACTO NAVAL)',
                    'PROYECTO DE VENTA (SERV. INDUSTRIA)',
                    'PROYECTO DE VENTA (SUMINISTRO/SERVICIO)',
                    'PROYECTO DE INVERSION INTERNA',
                    'PROYECTO DE INVERSIÓN (ARTEFACTO NAVAL)'
                ])->nullable();
            $table->string('SAP_code')->nullable()->unique();
            $table->enum('status',
            [
                'DISEÑO Y CONSTRUCCIÓN',
                'CONSTRUCCIÓN',
                'DISEÑO',
                'GARANTIA',
                'SERVICIO POSTVENTA'
            ])->nullable(); //Estado del proyecto
            $table->enum('scope',
            [
                'ADQUISICIÓN Y ENTREGA',
                'CO DESARROLLO DISEÑO Y CONSTRUCCIÓN',
                'CO PRODUCCIÓN',
                'CONSTRUCCIÓN',
                'DISEÑO BUQUE',
                'DISEÑO Y CONSTRUCCIÓN',
                'SERVICIOS INDUSTRIALES'
            ])->nullable(); //Alcance del projecto
            $table->string('supervisor')->nullable();
            $table->double('cost_sale')->nullable();
            $table->string('description')->nullable();
            $table->string('gerencia');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->float('hoursPerDay')->default(floatval(8.5));
            $table->integer('daysPerWeek')->default(5);
            $table->integer('daysPerMonth')->default(20);
            $table->integer('shift')->nullable(); //Turno del proyecto
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
