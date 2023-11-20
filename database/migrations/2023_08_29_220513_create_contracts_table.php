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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_id')->unique();
            $table->string('subject')->unique();
            $table->unsignedBigInteger('customer_id')->index();
            $table->bigInteger('manager_id')->nullable();
            $table->enum('type_of_sale', ['VENTA DIRECTA', 'FINANCIADA', 'LEASING'])->nullable();
            $table->string('supervisor')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('currency', ['COP', 'USD', 'EUR'])->nullable();
            $table->double('cost');
            $table->enum('state', ['LIQUIDADO', 'EN EJECUCIÓN']);
            $table->foreign('customer_id')->references('id')->on('customers');
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
        Schema::dropIfExists('contracts');
    }
};
