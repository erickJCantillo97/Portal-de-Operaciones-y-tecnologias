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
        Schema::create('sub_systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('system_id')->index();
            $table->foreign('system_id')->references('id')->on('systems');
            $table->string('code',45)->unique();
            $table->string('name',200);
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
        Schema::dropIfExists('sub_systems');
    }
};
