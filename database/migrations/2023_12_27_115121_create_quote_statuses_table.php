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
        Schema::create('quote_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quote_version_id');
            $table->integer('user_id')->index()->unsigned();
            $table->integer('status');
            $table->dateTime('fecha');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_statuses');
    }
};
