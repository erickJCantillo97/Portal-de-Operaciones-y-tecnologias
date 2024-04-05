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
            $table->string('note', 4000)->nullable();
            $table->string('bloque');
            $table->string('type');
            $table->string('sistema_grupo');
            $table->date('preeliminar_date');
            $table->integer('approved_dipr_user')->nullable();
            $table->date('approved_dipr_date')->nullable();
            $table->integer('approved_ppc_user')->nullable();
            $table->date('approved_ppc_date')->nullable();
            $table->integer('supervisor_num_sap')->nullable();
            $table->integer('approved_manager_user')->nullable();
            $table->date('approved_manager_date')->nullable();
            $table->string('operation_id')->nullable();
            $table->string('num_solped')->nullable();
            $table->date('oficial_date')->nullable();
            $table->bigInteger('intendente_id')->index()->nullable();
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
