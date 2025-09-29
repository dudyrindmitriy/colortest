<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestingStatistic;
use App\Models\Results;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateTestingStatistics extends Command
{
    protected $signature = 'generate:test-statistics';
    protected $description = 'Generate testing statistics for analysis';

    public function handle()
    {
        // Очистка старых данных
        TestingStatistic::truncate();

        $start = Carbon::create(2023, 3, 1);
        $end = Carbon::create(2024, 3, 31);

        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            
            // Получаем все записи за день
            $dailyResults = Results::whereDate('created_at', $date)->get();
            
            // Рассчитываем средний match в PHP
            $totalMatch = 0;
            $count = 0;
            
            foreach ($dailyResults as $result) {
                // Парсим значение match (формат: "стиль-процент")
                if (preg_match('/-(\d+)/', $result->match, $matches)) {
                    $totalMatch += (int)$matches[1];
                    $count++;
                }
            }
            
            $averageMatch = $count > 0 ? $totalMatch / $count : 0;
            
            // Собираем распределение стилей
            $styleDistribution = $dailyResults->groupBy('industry')
                ->map->count()
                ->toArray();

            // Создаем запись статистики
            TestingStatistic::create([
                'period_date' => $date,
                'tests_count' => $dailyResults->count(),
                'average_match' => $averageMatch,
                'new_users' => User::whereHas('results', function($query) use ($date) {
                    $query->whereDate('created_at', $date);
                })
                ->distinct()
                ->count(),
                'style_distribution' => $styleDistribution
            ]);

            $start->addDay();
            
            // Вывод прогресса
            $this->info("Processed date: {$date}");
        }

        $this->info('Testing statistics generated successfully!');
    }
}