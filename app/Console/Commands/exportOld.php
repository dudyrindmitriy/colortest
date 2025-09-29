<?php
namespace App\Console\Commands;

use App\Models\Results;
use Illuminate\Console\Command;
use League\Csv\Writer;

class EExportTrainingData extends Command
{
    protected $signature = 'export:training-data';
    
    // Добавлены константы для полей
    private const STATS_FIELDS = ['r', 'g', 'b', 'x', 'y', 'z'];
    private const CSV_HEADERS = [
        'r_mean', 'r_std', 'r_min', 'r_max', 'r_median',
        'g_mean', 'g_std', 'g_min', 'g_max', 'g_median',
        'b_mean', 'b_std', 'b_min', 'b_max', 'b_median',
        'x_mean', 'x_std', 'x_min', 'x_max', 'x_median',
        'y_mean', 'y_std', 'y_min', 'y_max', 'y_median',
        'z_mean', 'z_std', 'z_min', 'z_max', 'z_median',
        'industry', 'chess_structure'
    ];

    public function handle()
    {
        $targetDate = '2025-01-25';
        
        $results = Results::with(['rectanglesForResult'])
            ->whereDate('created_at', $targetDate)
            ->get();
        
        $csv = Writer::createFromString('');
        $csv->insertOne(self::CSV_HEADERS);
        
        foreach ($results as $result) {
            $features = $this->extractFeatures($result->rectanglesForResult);
            $csv->insertOne([
                ...array_values($features),
                $result->industry,
                $result->chess_structure
            ]);
        }
        
        file_put_contents('training_data.csv', $csv->toString());
    }

    private function extractFeatures($rectangles): array
    {
        $features = [];
        
        foreach (self::STATS_FIELDS as $field) {
            $values = $this->collectFieldValues($rectangles, $field);
            $features += $this->calculateStatistics($field, $values);
        }
        
        return $features;
    }

    private function collectFieldValues($rectangles, string $field): array
    {
        return array_map(function($rect) use ($field) {
            if (in_array($field, ['r', 'g', 'b'])) {
                $rgb = $this->extractRgb($rect->color);
                return $rgb[$field];
            }
            return $rect->{$field};
        }, $rectangles->all());
    }

    private function calculateStatistics(string $prefix, array $values): array
    {
        sort($values);
        $count = count($values);
        
        return [
            "{$prefix}_mean" => round(array_sum($values) / $count),
            "{$prefix}_std" => round($this->calculateStdDev($values, $count), 2),
            "{$prefix}_min" => min($values),
            "{$prefix}_max" => max($values),
            "{$prefix}_median" => $this->calculateMedian($values, $count)
        ];
    }

    private function extractRgb(string $color): array
    {
        preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches);
        return [
            'r' => (int)$matches[1],
            'g' => (int)$matches[2],
            'b' => (int)$matches[3]
        ];
    }

    private function calculateStdDev(array $values, int $count): float
    {
        if ($count < 2) return 0.0;
        
        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(
            fn($x) => pow($x - $mean, 2), $values
        )) / ($count - 1);
        
        return sqrt($variance);
    }

    private function calculateMedian(array $sortedValues, int $count)
    {
        if ($count === 0) return 0;
        
        $mid = (int)floor(($count - 1) / 2);
        return $count % 2 
            ? $sortedValues[$mid]
            : ($sortedValues[$mid] + $sortedValues[$mid + 1]) / 2;
    }
}