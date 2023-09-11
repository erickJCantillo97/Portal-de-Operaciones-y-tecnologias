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
        Schema::create('specific_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_id')->unsigned();
            $table->foreign('process_id')->references('id')->on('processes');
            $table->string('name');
            $table->unsignedBigInteger('ord')->unsigned()->default(0);
            $table->boolean('validity')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specific_activities');
    }
};
