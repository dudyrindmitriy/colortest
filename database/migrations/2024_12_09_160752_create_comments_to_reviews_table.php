<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsToReviewsTable extends Migration
{

    public function up()
    {
        Schema::create('comment_to_reviews', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('review_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->text('comment_text');
            $table->timestamps(); 
        });
    }


    public function down()
    {
        Schema::dropIfExists('comment_to_reviews');
    }
}