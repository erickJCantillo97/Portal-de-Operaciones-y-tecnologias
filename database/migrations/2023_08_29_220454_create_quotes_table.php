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
            $table->string('gerencia');
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->bigInteger('estimador_id')->unsigned()->index();
            $table->integer('consecutive');
            $table->integer('version');
            $table->date('expeted_answer_date');
            $table->date('estimador_anaswer_date')->nullable();
            $table->string('offer_type')->nullable();
            $table->text('route')->nullable();
            $table->string('file')->nullable();
            $table->string('estimador_name');
            $table->text('observation')->nullable();
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
