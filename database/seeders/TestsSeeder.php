<?php

namespace Database\Seeders;

use App\Models\Test;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $tests = [
            // цветовой тест
            [
                'code' => 'color_test',
                'name' => 'Цветовое тестирование',
            ],

            // 11 классических тестов
            [
                'code' => 'holland',
                'name' => 'Методика Дж. Холланда',
            ],
            [
                'code' => 'interest_map',
                'name' => 'Карта интересов',
            ],
            [
                'code' => 'ddo',
                'name' => 'Дифференциально-диагностический опросник Е.А.Климова',
            ],
            [
                'code' => 'jovayshi',
                'name' => 'Опросник профессиональных склонностей Л. Йовайши',
            ],
            [
                'code' => 'profession_matrix',
                'name' => 'Матрица профессий',
            ],
            [
                'code' => 'eisenck_state',
                'name' => 'Самооценка психических состояний (Айзенк)',
            ],
            [
                'code' => 'jung_character',
                'name' => 'Определение типа характера по К. Юнгу',
            ],
            [
                'code' => 'thomas_conflict',
                'name' => 'Тест Томаса - типы поведения в конфликте',
            ],
            [
                'code' => 'keirsey',
                'name' => 'Опросник Д. Кейрси',
            ],
            [
                'code' => 'leader_organizer',
                'name' => 'Организаторские способности лидера',
            ],
            [
                'code' => 'self_development',
                'name' => 'Способность к саморазвитию и самообразованию',
            ],
        ];

        foreach ($tests as $test) {
            Test::firstOrCreate(
                ['code' => $test['code']],
                $test
            );
        }
    }
}
