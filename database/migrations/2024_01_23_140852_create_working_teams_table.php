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
        Schema::create('working_teams', function (Blueprint $table) {
            $table->id();
            $table->integer('user_num_sap');
            $table->bigInteger('team_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            // $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('team_id')->references('id')->on('teams');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_teams');
    }
};
