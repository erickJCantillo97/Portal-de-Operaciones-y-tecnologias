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
        Schema::create('personals', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index()->unsigned();
            $table->integer('boss_id')->index()->unsigned();
            $table->integer('boss_last_id')->nullable();
            $table->string('gerencia_lent')->nullable();
            $table->string('oficina_lent')->nullable();
            $table->date('return_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personals');
    }
};
