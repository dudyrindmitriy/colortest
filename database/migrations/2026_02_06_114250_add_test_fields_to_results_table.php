<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->unsignedBigInteger('test_id')
                ->nullable()
                ->after('user_id');

            $table->json('data')
                ->nullable()
                ->after('user_image');

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->dropForeign(['test_id']);

            $table->dropColumn([
                'test_id',
                'data',
            ]);
        });
    }
};
