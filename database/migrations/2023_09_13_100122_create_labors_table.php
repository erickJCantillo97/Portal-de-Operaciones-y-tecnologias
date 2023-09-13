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
        Schema::create('labors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gerencia');
            $table->double('costo_mes');
            $table->double('costo_dia');
            $table->double('costo_hora');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labors');
    }
};
