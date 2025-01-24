<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chess_structure', function (Blueprint $table) {
            $table->id(); 
            $table->longText('image')->nullable(); 
            $table->timestamps(); 
        });

     
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedBigInteger('chess_structure_id')->nullable(); 
            $table->foreign('chess_structure_id')->references('id')->on('chess_structure')->onDelete('set null'); 
        });
    }

    public function down()
    {
       
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['chess_structure_id']);
            $table->dropColumn('chess_structure_id');
        });

       
        Schema::dropIfExists('chess_structure');
    }
};
