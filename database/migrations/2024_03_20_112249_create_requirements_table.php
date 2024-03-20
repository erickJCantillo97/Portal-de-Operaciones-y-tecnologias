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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->string('consecutive')->unique();
            $table->integer('project_id')->index();
            $table->bigInteger('user_id')->index();
            $table->string('document')->nullable();
            $table->string('note')->nullable();
            $table->string('bloque');
            $table->string('sistema_grupo');
            $table->date('preeliminar_date');
            $table->date('oficial_date')->nullable();
            $table->date('overcome_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
