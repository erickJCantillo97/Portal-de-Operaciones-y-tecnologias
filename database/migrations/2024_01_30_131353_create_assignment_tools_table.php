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
        Schema::create('assignment_tools', function (Blueprint $table) {
            $table->id();
            $table->integer('tool_id')->index();
            $table->integer('employee_id')->index();
            $table->integer('project_id')->index();
            $table->integer('supervisor_id')->index();
            $table->integer('user_receive')->index()->nullable();
            $table->integer('user_deliver')->index();
            $table->string('location')->index();
            $table->string('system')->nullable();
            $table->integer('block')->nullable();
            $table->Date('assigment_date');
            $table->Date('return_date_expected')->nullable();
            $table->date('return_date')->nullable();
            $table->string('status')->default('ASIGNADO');
            $table->string('observation')->nullable();
            $table->string('gerencia');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_tools');
    }
};
