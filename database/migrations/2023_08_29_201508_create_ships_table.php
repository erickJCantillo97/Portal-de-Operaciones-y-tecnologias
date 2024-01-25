<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index()->nullable();
            $table->unsignedBigInteger('type_ship_id')->nullable();
            $table->string('name');
            $table->string('idHull')->nullable();
            $table->integer('quilla')->nullable();
            $table->integer('pantoque')->nullable();
            $table->double('eslora')->nullable();
            $table->string('acronyms')->nullable();
            $table->string('file')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('type_ship_id')->references('id')->on('type_ships');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ships');
    }
};
