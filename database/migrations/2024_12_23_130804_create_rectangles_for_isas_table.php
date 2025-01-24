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
        Schema::create('rectangles_for_isas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('isa_id');
            $table->string('color', 20);
            $table->integer('x'); 
            $table->integer('y'); 
            $table->integer('z'); 
            $table->timestamps();
            $table->foreign('isa_id')->references('id')->on('isas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rectangles_for_isas');
    }
};
