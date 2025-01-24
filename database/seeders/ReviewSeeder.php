<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        // Получаем всех пользователей
        $users = User::all();

        // Генерируем 10 отзывов
        Review::factory()->count(10)->create([
            'user_id' => $users->random()->id, // Присваиваем случайного пользователя
        ]);
    }
}