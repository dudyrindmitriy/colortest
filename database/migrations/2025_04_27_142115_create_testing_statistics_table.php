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
        Schema::create('testing_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('period_date')->comment('Дата периода (месяц/неделя)');
            $table->integer('tests_count')->default(0)->comment('Количество тестов');
            $table->float('average_match')->default(0)->comment('Средний показатель match');
            $table->integer('new_users')->default(0)->comment('Новые пользователи');
            $table->json('style_distribution')->nullable()->comment('Распределение стилей');
            $table->timestamps();
            
            $table->index('period_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_statistics');
    }
};
