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
        Schema::table('projects', function (Blueprint $table) {
            $table->uuid('uuid')->default(Str::uuid())->nullable();
        });

        $rows = DB::table('projects')->get();
        foreach ($rows as $items) {
            DB::table('projects')->where('uuid', $items->uuid)->update(['uuid' => Str::uuid()]);
        }
        // Schema::table('projects', function (Blueprint $table) {
        //     $table->uuid('uuid')->unique()->change();
        // });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            //
        });
    }
};
