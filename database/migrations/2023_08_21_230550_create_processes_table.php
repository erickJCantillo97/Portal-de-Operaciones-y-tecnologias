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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subsystem_id')->unsigned();
            $table->foreign('subsystem_id')->references('id')->on('sub_systems');
            $table->enum("maintenance_type",['PREDICTIVO O DIAGNOSTICO', 'PREVENTIVO', 'CORRECTIVO', 'CAMBIO, REEMPLAZO O MONTAJE']);
            $table->string('name');
            $table->boolean('validity')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};
