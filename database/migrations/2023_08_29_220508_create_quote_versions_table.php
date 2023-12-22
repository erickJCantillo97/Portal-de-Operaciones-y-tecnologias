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
        Schema::create('quote_versions', function (Blueprint $table) {
            $table->id();
            $table->Biginteger('quote_id')->unsigned()->index();
            $table->Biginteger('version')->default(1);
            $table->bigInteger('estimador_id')->unsigned()->index();
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->date('expeted_answer_date');
            $table->date('estimador_anaswer_date')->nullable();
            $table->string('offer_type')->nullable();
            $table->string('estimador_name');
            $table->text('route')->nullable();
            $table->string('file')->nullable();
            $table->text('observation')->nullable();
            $table->foreign('quote_id')->references('id')->on('quotes');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_versions');
    }
};
