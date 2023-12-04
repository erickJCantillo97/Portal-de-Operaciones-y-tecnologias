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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quotes_id')->nullable()->index()->unsigned();
            $table->bigInteger('type_ship_id')->index()->unsigned()->nullable();
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->bigInteger('estimador_id')->unsigned()->index();
            $table->integer('consecutive');
            $table->integer('version');
            $table->string('name');
            $table->date('expeted_answer_date');
            $table->date('estimador_anaswer_date')->nullable();
            $table->string('scope')->nullable();
            $table->string('project_type')->nullable();
            $table->string('maturity')->nullable();
            $table->integer('units')->nullable();
            $table->string('coin')->nullable();
            $table->double('rate_buy_usd')->nullable();
            $table->double('rate_buy_eur')->nullable();
            $table->double('price_before_iva_original')->nullable();
            $table->string('iva')->nullable();
            $table->string('offer_type')->nullable();
            $table->double('margin')->nullable();
            $table->string('white_paper')->default('PENDIENTE');
            $table->string('no_white_paper')->nullable();
            $table->double('price_before_iva_cop')->nullable();
            $table->double('real_cost')->nullable();
            $table->string('gerencia');
            $table->text('route')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
