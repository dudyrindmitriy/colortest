<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\CommentToReview;

class CommentToReviewFactory extends Factory
{
    protected $model = CommentToReview::class;

    public function definition()
    {
        return [
            'comment_text' => $this->faker->sentence,
            'review_id' => 1, // Можно временно установить фиксированный ID отзыва
            'user_id' => 1, // Можно временно установить фиксированного пользователя
        ];
    }
}