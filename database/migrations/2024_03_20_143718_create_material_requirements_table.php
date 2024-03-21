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
        Schema::create('material_requirements', function (Blueprint $table) {
            $table->id();
            $table->integer('requirement_id');
            $table->integer('requirement_overcome_id')->nullable();
            $table->integer('material_id');
            $table->double('count');
            $table->string('status')->nullable();
            $table->string('unit');
            $table->string('observation', 4000)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requirements');
    }
};
