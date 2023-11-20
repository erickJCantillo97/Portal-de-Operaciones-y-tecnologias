<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('startShift');
            $table->dateTime('endShift');
            $table->dateTime('startBreak')->nullable;
            $table->dateTime('endBreak')->nullable;
            $table->boolean('status')->default(true);
            $table->string('description')->nullable;
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE shifts ADD hours AS (((DATEDIFF(s, startShift, endShift) - DATEDIFF(s, ISNULL(startBreak, 0), ISNULL(endBreak, 0))))/3600.0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
