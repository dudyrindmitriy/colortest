<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Isa;
use App\Models\Chess;
use App\Models\Results;
use App\Models\RectanglesForResult;
use App\Models\TestingStatistic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GenerateTestDataCommand extends Command
{
    protected $signature = 'generate:test-data 
                            {--force : Skip confirmation}';
    protected $description = 'Generate realistic test data for March 2023 - March 2024 period';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will delete all existing test data. Continue?')) {
            return;
        }

        $this->deleteOldData();
        $this->generateNewData();

        $this->info('Successfully generated 100 test records for period 2023-03 - 2024-03');
        TestingStatistic::truncate();

        $start = Carbon::create(2023, 3, 1);
        $end = Carbon::create(2024, 3, 31);

        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            
            // Получаем все записи за день
            $dailyResults = Results::whereDate('created_at', $date)->get();
            
      
            $maxMatch = 0;
            foreach ($dailyResults as $result) {
                // Инициализируем переменную для хранения максимального значения
                $currentMax = 0;
            
                // Извлекаем все значения процентов
                if (preg_match_all('/-(\d+\.\d+)/', $result->match, $matches)) {
                    foreach ($matches[1] as $percentage) {

                        $value = (float)$percentage;
                        if ($value >= 0 && $value <= 100) {
                            // Обновляем максимальное значение
                            if ($value > $currentMax) {
                                $currentMax = $value;
                            }
                        }
                    }
                }
                
                // Сохраняем максимальное значение для этого результата
                if ($currentMax > $maxMatch) {
                    $maxMatch = $currentMax;
                }
            }
            
            // Если не найдено ни одного значения, устанавливаем 0
            $maxMatch = $maxMatch ?? 0;
            
            // Собираем распределение стилей
            $styleDistribution = $dailyResults->groupBy('industry')
                ->map->count()
                ->toArray();

            // Создаем запись статистики
            TestingStatistic::create([
                'period_date' => $date,
                'tests_count' => $dailyResults->count(),
                'average_match' => $maxMatch,
                'new_users' => User::whereHas('results', function($query) use ($date) {
                    $query->whereDate('created_at', $date);
                })
                ->distinct()
                ->count(),
                'style_distribution' => $styleDistribution
            ]);

            $start->addDay();
            $this->info("percentage: {$maxMatch}");
            
            // Вывод прогресса
            $this->info("Processed date: {$date}");
        }

        $this->info('Testing statistics generated successfully!');
    }

    private function deleteOldData()
    {
        $this->info('Generating new data...');
        
        DB::transaction(function () {
            Results::where(function($query) {
                $query->whereBetween('created_at', [
                        '2023-03-01 00:00:00',
                        '2024-03-31 23:59:59'
                    ])
                    ->orWhere('created_at', '>', '2025-04-26 23:59:59');
            })->delete();
        });
    }

    private function generateNewData()
    {
        $faker = Faker::create('ru_RU');
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->error('No users found! Create users first.');
            return;
        }
        $selectedUsers = $users->count() > 7 
        ? $users->random(7) 
        : $users;
        $isas = Isa::all();
        $chessLevels = Chess::all();
        $colors = $this->getColorPalette();
        $rectanglesCount = $this->getRectangleCount();

        $progressBar = $this->output->createProgressBar(100);
        $progressBar->start();
        $userIndex = 0;
        $usersCount = $selectedUsers->count();
        for ($i = 0; $i < 100; $i++) {
            $user = $selectedUsers[$userIndex % $usersCount];
            $userIndex++;
            $this->createTestResult(
                user: $user,
                isas: $isas,
                chessLevels: $chessLevels,
                colors: $colors,
                rectanglesCount: $rectanglesCount,
                faker: $faker
            );
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function createTestResult($user, $isas, $chessLevels, $colors, $rectanglesCount, $faker)
    {
        $monthWeights = [
            1 => 0.5,   // Январь
            2 => 0.5,   // Февраль
            3 => 1.0,   // Март
            4 => 1.0,   // Апрель
            5 => 1.0,   // Май
            6 => 1.0,   // Июнь
            7 => 1.0,   // Июль
            8 => 2.0,   // Август
            9 => 3.0,   // Сентябрь (пик)
            10 => 1.0,  // Октябрь
            11 => 1.0,  // Ноябрь
            12 => 0.5   // Декабрь
        ];
    
        $date = $this->generateWeightedDate($monthWeights);

        $isa = $isas->random();

        $result = new Results([
            'user_id' => $user->id,
            'isa_id' => $isa->id,
            'industry' => $isa->individual_style_of_activity,
            'recommendation' => $this->generateRecommendation($isa->individual_style_of_activity),
            'user_image' => '<svg>...</svg>',
            'match' => $this->generateMatchString($isas, $user),
            'chess_structure' => $chessLevels->random()->chess_structure,
            'chess_structure_id' => $chessLevels->random()->id,
        ]);
        $result->created_at = $date;
        $result->updated_at = $date;
        $result->save();

        $this->createRectangles($result, $colors, $rectanglesCount, $date);
    }

    private function generateWeightedDate($monthWeights)
{
    $totalWeight = array_sum($monthWeights);
    $rand = mt_rand() / mt_getrandmax() * $totalWeight;
    $cumulative = 0;

    // Выбираем месяц по весу
    foreach ($monthWeights as $month => $weight) {
        $cumulative += $weight;
        if ($rand <= $cumulative) {
            $year = ($month >= 3) ? 2023 : 2024; 
            $daysInMonth = Carbon::create($year, $month)->daysInMonth;
            
            return Carbon::create(
                $year,
                $month,
                rand(1, $daysInMonth),
                rand(0, 23),
                rand(0, 59),
                rand(0, 59)
            );
        }
    }

    return Carbon::create(2023, 9, 1);
}

    private function createRectangles($result, $colors, $count, $date)
    {
        for ($j = 0; $j < $count; $j++) {
            $rectangle = new RectanglesForResult([
                'result_id' => $result->id,
                'color' => $colors[array_rand($colors)],
                'x' => rand(1, 10),
                'y' => rand(1, 3),
                'z' => rand(1, 10),
                'created_at' => $date,
                'updated_at' => $date
            ]);
            $rectangle->created_at = $date;
            $rectangle->updated_at = $date;
            $rectangle->save();
        }
    }

    private function getColorPalette()
    {
        return [
            'rgb(255, 0, 0)', 'rgb(0, 255, 0)', 'rgb(0, 0, 255)',
            'rgb(255, 255, 0)', 'rgb(255, 0, 255)', 'rgb(0, 255, 255)',
            'rgb(255, 255, 255)', 'rgb(0, 0, 0)'
        ];
    }

    private function getRectangleCount()
    {
        return Results::first()
            ? RectanglesForResult::where('result_id', Results::first()->id)->count()
            : 8;
    }

    private function generateRecommendation($style)
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

    private function generateMatchString($isas, $user) 
    {
        // Фиксируем базовые значения для каждого пользователя
        $baseScores = [
            'творец' => rand(60, 90),
            'авангардист' => rand(55, 85),
            'смешанный' => rand(50, 80),
            'рационал' => rand(65, 95),
            'скептик' => rand(40, 75)
        ];
    
        // Добавляем отклонение, связанное с характеристиками пользователя
        $userFactor = $user->created_at->diffInMonths() * 0.5; // Чем старше аккаунт, тем выше %
        $noise = rand(-5, 5);
    
        $matches = [];
        foreach ($isas as $isa) {
            $style = $isa->individual_style_of_activity;
            $base = $baseScores[$style] ?? 50;
            $score = min(100, max(10, $base + $userFactor + $noise));
            
            $matches[] = "{$style}-{$score}";
        }
    
        return implode("     ", $matches);
    }
}