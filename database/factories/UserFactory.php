<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'login' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail, // Генерация уникального email
            'password' => bcrypt('password'), // Простой пароль для теста
            'address' => $this->faker->address,
            'isAdmin' => false,
        ];
    }
}