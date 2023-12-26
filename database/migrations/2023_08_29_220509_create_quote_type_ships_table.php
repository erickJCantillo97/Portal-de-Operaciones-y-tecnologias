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
        Schema::create('quote_type_ships', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quote_version_id')->index()->unsigned();
            $table->bigInteger('type_ship_id')->index()->unsigned();
            $table->string('name');
            $table->string('scope')->nullable();
            $table->string('project_type')->nullable();
            $table->string('maturity')->nullable();
            $table->integer('units')->nullable();
            $table->string('coin')->nullable();
            $table->double('rate_buy_usd')->nullable();
            $table->double('rate_buy_eur')->nullable();
            $table->double('price_before_iva_original')->nullable();
            $table->string('iva')->nullable();
            $table->double('margin')->nullable();
            $table->string('white_paper')->default('PENDIENTE');
            $table->string('white_paper_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_type_ships');
    }
};
