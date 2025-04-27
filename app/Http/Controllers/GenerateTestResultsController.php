<?php

namespace App\Http\Controllers;

use App\Models\Isa;
use App\Models\Chess;
use App\Models\Results;
use App\Models\RectanglesForResult;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class GenerateTestResultsController extends Controller
{
    public function generate()
    {
        // Удаление некорректных результатов (апрель 2025)
        Results::where('created_at', '>=', '2025-04-26')->delete();

        $faker = Faker::create('ru_RU');
        $isas = Isa::all();
        $chessLevels = Chess::all();
        $colors = ['rgb(255, 0, 0)', 'rgb(0, 255, 0)', 'rgb(0, 0, 255)', 
                  'rgb(255, 255, 0)', 'rgb(255, 0, 255)', 'rgb(0, 255, 255)',
                  'rgb(255, 255, 255)', 'rgb(0, 0, 0)'];

        // Получаем количество прямоугольников из существующих данных
        $rectanglesCount = RectanglesForResult::first() 
            ? RectanglesForResult::first()->where('result_id', Results::first()->id)->count()
            : 8;

        // Генерация 100 результатов
        for ($i = 0; $i < 100; $i++) {
            // Генерация даты между 2023-03-01 и 2024-03-31
            $createdAt = $faker->dateTimeBetween('2023-03-01', '2024-03-31');
            
            // Создание результата с явным указанием временных меток
            $result = new Results([
                'user_id' => 1,
                'isa_id' => $isas->random()->id,
                'industry' => $isas->random()->individual_style_of_activity,
                'recommendation' => $this->generateFakeRecommendation($isas->random()->individual_style_of_activity),
                'user_image' => '<svg>...</svg>',
                'match' => $this->generateFakeMatchString($isas),
                'chess_structure' => $chessLevels->random()->chess_structure,
                'chess_structure_id' => $chessLevels->random()->id,
               
            ]);
            $result->created_at = $createdAt;
            $result->updated_at = $createdAt;
            $result->save();
            // Генерация прямоугольников
            for ($j = 0; $j < $rectanglesCount; $j++) {
                $rectangle = new RectanglesForResult([
                    'result_id' => $result->id,
                    'color' => $colors[array_rand($colors)],
                    'x' => rand(1, 10),
                    'y' => rand(1, 3),
                    'z' => rand(1, 10),
                   
                ]);
                $rectangle->created_at = $createdAt;
                $rectangle->updated_at = $createdAt;
                $rectangle->save();
            }
        }

        return "Удалены ошибочные записи и сгенерировано 100 новых результатов с марта 2023 по март 2024";
    }

    private function generateFakeRecommendation($style)
    {
        $recommendations = [
            'творец' => '<ol><li>Программная инженерия</li><li>Актерское искусство</li><li>Дизайн</li></ol>',
            'авангардист' => '<ol><li>Математика и компьютерные науки</li><li>Фундаментальная информатика</li><li>Фотоника</li></ol>',
            'смешанный' => '<ol><li>Программная инженерия</li><li>Информатика</li><li>Прикладная математика</li></ol>',
            'рационал' => '<ol><li>Фундаментальная информатика</li><li>Физика/Химия</li><li>Фотоника</li></ol>',
            'скептик' => '<ol><li>Психология</li><li>Социология</li><li>Социальная работа</li></ol>',
        ];

        return $recommendations[$style] ?? $recommendations['смешанный'];
    }

    private function generateFakeMatchString($isas)
    {
        $matches = [];
        foreach ($isas as $isa) {
            $matches[] = $isa->individual_style_of_activity . "-" . rand(10, 90);
        }
        return implode("     ", $matches);
    }
}