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
        Schema::table('chess_structure', function (Blueprint $table) {
            $table->string('chess_structure')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chess_structure', function (Blueprint $table) {
            $table->dropColumn('chess_structure');
        });
    }
};
