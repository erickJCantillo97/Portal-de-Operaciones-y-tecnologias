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
            $table->bigInteger('quotes_id')->nullable()->index()->unsigned();
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
            $table->string('no_white_paper')->nullable();
            $table->double('price_before_iva_cop')->nullable();
            $table->double('real_cost')->nullable();

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
