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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('responsible_id')->index()->nullable()->unsigned();
            $table->integer('category_id')->index()->unsigned(); //3 Listas
            // $table->integer('almacen_id')->index()->nullable();
            $table->string('gerencia');
            // $table->string('name');
            $table->string('code')->unique(); //creado por el Back
            $table->string('serial')->unique()->nullable();
            $table->string('SAP_code')->nullable();
            $table->string('value')->nullable();
            $table->string('brand')->nullable()->index();
            $table->string('entry_date')->nullable();
            $table->boolean('is_small')->default(false);
            $table->string('estado')->default('DISPONIBLE');
            $table->string('estado_operativo')->default('OPERATIVA');
            // $table->integer('criticidad')->nullable();
            // $table->integer('capacidad')->nullable();
            // $table->string('tipo_falla')->nullable();
            // $table->double('criticidad_total')->nullable();
            $table->text('description')->nullable();
            $table->text('imagen')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
