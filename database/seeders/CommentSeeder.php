<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;
use App\Models\CommentToReview;
use App\Models\Review;
use App\Models\User;

class CommentSeeder extends Seeder
{
    public function run()
    {
        // Создаем комментарии к отзывам
        $reviews = Review::all();
        $users = User::all();

        foreach ($reviews as $review) {
            // Создаем от 1 до 3 комментариев для каждого отзыва
            for ($i = 0; $i < rand(1, 3); $i++) {
                CommentToReview::factory()->create([
                    'review_id' => $review->id,
                    'user_id' => $users->random()->id,
                    'comment_text' => 'Тестовый комментарий ' . $i . ' для отзыва ' . $review->id,
                ]);
            }
        }
    }
}