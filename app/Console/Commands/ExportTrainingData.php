<?php

namespace App\Console\Commands;

use App\Models\Isa;
use App\Models\Results;
use Illuminate\Console\Command;
use League\Csv\Writer;

class ExportTrainingData extends Command
{

    protected $signature = 'generate:training-data';

    public const WALL_MAP = [
        1 => 'left_wall',
        2 => 'ceiling',
        3 => 'right_wall',
        4 => 'floor'
    ];
    public const DEFAULT_MAX = [
        'color_var' => 10000.0,
        'depth_grad' => 25.0,
        'lum_std' => 100.0,
        'contrast' => 400.0,
        'symmetry' => 4000.0,
        'pattern' => 1.0,
        'color_distance' => 400.0
    ];

    public const CSV_HEADERS = [
        'left_wall_color_var',
        'left_wall_depth_grad',
        'left_wall_lum_std',
        'ceiling_color_var',
        'ceiling_depth_grad',
        'ceiling_lum_std',
        'right_wall_color_var',
        'right_wall_depth_grad',
        'right_wall_lum_std',
        'floor_color_var',
        'floor_depth_grad',
        'floor_lum_std',
        'vertical_symmetry',
        'horizontal_symmetry',
        'vertical_contrast',
        'horizontal_contrast',
        'depth_contrast',
        'pattern_consistency',
        'education_program'
    ];

    public function handle()
    {
        $results = Results::with(['user.educationProgram'])
            ->whereHas('user', function ($query) {
                $query->where('user_type', 'student');
            })
            ->get();
        $csv = Writer::createFromString('');
        $csv->insertOne(self::CSV_HEADERS);

        foreach ($results as $result) {
            $this->processResult($result, $csv);
        }

        file_put_contents('app/Services/training_data.csv', $csv->toString());
    }

    public function processResult($result, $csv)
    {
        $walls = $this->groupRectanglesByWall($result->rectanglesForResult);
        $features = [];

        foreach (self::WALL_MAP as $x => $wallName) {
            $wallRects = $walls[$x] ?? [];
            $features += $this->processWall($wallName, $wallRects);
        }
        $symmetryMetrics = $this->calculateSymmetryMetrics($walls);
        $features = array_merge($features, $symmetryMetrics);

        $contrastMetrics = $this->calculateContrastMetrics($walls);
        $features = array_merge($features, $contrastMetrics);

        $patternMetrics = $this->calculatePatternMetrics($walls);
        $features = array_merge($features, $patternMetrics);

        $educationProgram = $result->user->educationProgram->code ?? 'unknown';
        $csv->insertOne([
            ...array_values($features),
            // $this->calculateChessStructureScore($patternMetrics),
            $educationProgram
        ]);
    }





    public function calculatePercentile(array $data, float $percentile): float
    {
        sort($data);
        $index = ($percentile / 100) * (count($data) - 1);
        $fraction = $index - floor($index);

        return $data[floor($index)] * (1 - $fraction) + $data[ceil($index)] * $fraction;
    }
    public function normalizeFeature(float $value, string $featureType): float
    {
        $max = self::DEFAULT_MAX[$featureType] ?? 1.0;
        return $value > 0 ? min(round(($value / $max) * 100, 2), 100) : 0;
    }



    public function colorVariance(array $colors, string $wallName): float
    {
        if (count($colors) < 2) return 0.0;

        $variances = [];
        foreach (['R' => 0, 'G' => 1, 'B' => 2] as $channel) {
            $values = array_column($colors, $channel);
            $variances[] = $this->calculateVariance($values);
        }

        $product = array_product($variances);
        $geometricMean = $product > 0 ? pow($product, 1 / 3) : 0;

        return $this->normalizeFeature($geometricMean, 'color_var');
    }
    // public function calculateChessStructureScore(array $metrics): string
    // {

    //     $adjusted = [
    //         'pattern' => $metrics['pattern_consistency'],
    //         'h_contrast' => $this->normalizeFeature($metrics['horizontal_contrast'], 'contrast'),
    //         'd_contrast' => $this->normalizeFeature($metrics['depth_contrast'], 'contrast')
    //     ];

    //     $score = ($adjusted['pattern'] * 0.5)
    //         + ($adjusted['h_contrast'] * 0.3)
    //         + ($adjusted['d_contrast'] * 0.2);

    //     return match (true) {
    //         $score > 35 => 'сильная',
    //         $score > 25 => 'средняя',
    //         default => 'слабая'
    //     };
    // }

    public function calculatePatternConsistency(array $walls): float
    {
        $chessCount = 0;
        $totalPairs = 0;
        $dynamicThreshold = $this->calculateDynamicThreshold($walls);

        foreach ($walls as $wallRects) {
            $grid = [];
            foreach ($wallRects as $rect) {
                $grid[$rect->y][$rect->z] = $this->extractColorComponents($rect);
            }

            foreach ($grid as $y => $zs) {
                ksort($zs);
                $prev = null;
                foreach ($zs as $z => $color) {
                    if ($prev !== null) {
                        $totalPairs++;
                        if ($this->colorDistance($prev, $color) > $dynamicThreshold) {
                            $chessCount++;
                        }
                    }
                    $prev = $color;
                }
            }
        }

        $rawConsistency = $totalPairs ? ($chessCount / $totalPairs) : 0;
        return $this->normalizeFeature($rawConsistency, 'pattern');
    }

    public function calculateDynamicThreshold(array $walls): float
    {
        $distances = [];
        foreach ($walls as $wallRects) {
            foreach ($wallRects as $rect) {
                foreach ($wallRects as $otherRect) {
                    if ($rect !== $otherRect) {
                        $distances[] = $this->colorDistance(
                            $this->extractColorComponents($rect),
                            $this->extractColorComponents($otherRect)
                        );
                    }
                }
            }
        }
        return count($distances) ? array_sum($distances) / count($distances) * 0.7 : 100;
    }

    public function calculateSymmetryMetrics(array $walls): array
    {
        $left = $walls[1] ?? [];
        $right = $walls[3] ?? [];
        $ceiling = $walls[2] ?? [];
        $floor = $walls[4] ?? [];

        return [
            'vertical_symmetry' => $this->calculateVerticalSymmetry($left, $right),
            'horizontal_symmetry' => $this->calculateHorizontalSymmetry($ceiling, $floor),
        ];
    }

    public function calculateVerticalSymmetry(array $left, array $right): float
    {
        $diff = 0;
        $pairs = 0;

        foreach ($left as $lRect) {
            foreach ($right as $rRect) {
                if ($lRect->y == $rRect->y && $lRect->z == $rRect->z) {
                    $distance = $this->colorDistance(
                        $this->extractColorComponents($lRect),
                        $this->extractColorComponents($rRect)
                    );
                    $diff += $this->normalizeFeature($distance, 'color_distance');
                    $pairs++;
                }
            }
        }
        if ($pairs === 0) return 0.0;

        $raw = $diff / $pairs;
        return $this->normalizeFeature(pow($raw, 2), 'symmetry');
    }

    public function calculateHorizontalSymmetry(array $top, array $bottom): float
    {
        $diff = 0;
        $pairs = 0;

        foreach ($top as $tRect) {
            foreach ($bottom as $bRect) {
                if ($tRect->x == $bRect->x && $tRect->z == $bRect->z) {
                    $distance = $this->colorDistance(
                        $this->extractColorComponents($tRect),
                        $this->extractColorComponents($bRect)
                    );
                    $diff += $this->normalizeFeature($distance, 'color_distance');
                    $pairs++;
                }
            }
        }

        if ($pairs === 0) return 0.0;

        $raw = $diff / $pairs;
        return $this->normalizeFeature(pow($raw, 2), 'symmetry');
    }
    public function calculateContrastMetrics(array $walls): array
    {
        return [
            'vertical_contrast' => $this->calculateVerticalContrast($walls),
            'horizontal_contrast' => $this->calculateHorizontalContrast($walls),
            'depth_contrast' => $this->calculateDepthContrast($walls)
        ];
    }
    public function calculateVerticalContrast(array $walls): float
    {
        $contrasts = [];

        foreach ($walls as $wallRects) {
            $sorted = [];
            foreach ($wallRects as $rect) {
                $sorted[$rect->x][$rect->z] = $this->extractColorComponents($rect);
            }

            foreach ($sorted as $x => $zs) {
                ksort($zs);
                $prev = null;
                foreach ($zs as $z => $color) {
                    if ($prev !== null) {
                        $contrasts[] = $this->colorDistance($prev, $color);
                    }
                    $prev = $color;
                }
            }
        }
        $filtered = array_filter($contrasts, fn($v) => $v > 10 && $v < 200);

        if (empty($filtered)) return 0.0;

        $avg = array_sum($filtered) / count($filtered);
        return $this->normalizeFeature($avg, 'contrast');
    }
    public function calculateHorizontalContrast(array $walls): float
    {
        $contrasts = [];
        foreach ($walls as $wallRects) {
            $sorted = [];
            foreach ($wallRects as $rect) {
                $sorted[$rect->z][$rect->y] = $this->extractColorComponents($rect);
            }

            foreach ($sorted as $z => $ys) {
                ksort($ys);
                $prev = null;
                foreach ($ys as $y => $color) {
                    if ($prev !== null) {
                        $contrasts[] = $this->colorDistance($prev, $color);
                    }
                    $prev = $color;
                }
            }
        }
        $filtered = array_filter($contrasts, fn($v) => $v > 10 && $v < 200);
        if (empty($filtered)) return 0.0;

        $avg = array_sum($filtered) / count($filtered);
        return $this->normalizeFeature($avg, 'contrast');
    }

    public function calculateDepthContrast(array $walls): float
    {
        $contrasts = [];
        foreach ($walls as $wallRects) {
            $sorted = [];
            foreach ($wallRects as $rect) {
                $sorted[$rect->y][$rect->z] = $this->extractColorComponents($rect);
            }

            foreach ($sorted as $y => $zs) {
                ksort($zs);
                $prev = null;
                foreach ($zs as $z => $color) {
                    if ($prev !== null) {
                        $contrasts[] = $this->colorDistance($prev, $color);
                    }
                    $prev = $color;
                }
            }
        }
        $filtered = array_filter($contrasts, fn($v) => $v > 10 && $v < 200);
        if (empty($filtered)) return 0.0;

        $avg = empty($filtered) ? 0 : array_sum($filtered) / count($filtered);
        return $this->normalizeFeature($avg, 'contrast');
    }



    public function groupRectanglesByWall($rectangles): array
    {
        $groups = [];
        foreach ($rectangles as $rect) {
            $groups[$rect->x][] = $rect;
        }
        return $groups;
    }

    public function processWall(string $wallName, array $rectangles): array
    {
        if (empty($rectangles)) return [];

        $colors = array_map([$this, 'extractColorComponents'], $rectangles);
        $depths = array_column($rectangles, 'z');
        $count = count($rectangles);
        return [
            "{$wallName}_color_var" => $this->colorVariance($colors, $wallName),
            "{$wallName}_depth_grad" => $this->depthGradient($colors, $depths),
            "{$wallName}_lum_std" => $this->luminanceStd($colors)
        ];
    }

    public function extractColorComponents($rect): array
    {
        preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $rect->color, $matches);
        return array_map('intval', array_slice($matches, 1));
    }


    public function calculateVariance(array $values): float
    {
        $n = count($values);
        if ($n < 2) return 0.0;

        $mean = array_sum($values) / $n;
        return array_sum(array_map(
            fn($v) => pow($v - $mean, 2),
            $values
        )) / ($n - 1);
    }

    public function depthGradient(array $colors, array $depths): float
    {
        if (count($depths) < 2) return 0.0;

        $gradients = [];
        foreach ([0, 1, 2] as $channel) {
            $values = array_column($colors, $channel);
            $slope = abs($this->calculateRobustSlope($depths, $values));
            $gradients[] = $slope;
        }

        $q1 = $this->calculatePercentile($gradients, 25);
        $q3 = $this->calculatePercentile($gradients, 75);
        $iqr = $q3 - $q1;
        $upperBound = $q3 + 1.5 * $iqr;

        $filtered = array_filter($gradients, fn($g) => $g <= $upperBound);
        return $this->normalizeFeature(max($filtered ?: [0]), 'depth_grad');
    }

    public function calculateRobustSlope(array $x, array $y): float
    {
        $n = count($x);
        if ($n !== count($y) || $n < 2) return 0.0;

        $slopes = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($x[$j] != $x[$i]) {
                    $slopes[] = ($y[$j] - $y[$i]) / ($x[$j] - $x[$i]);
                }
            }
        }

        sort($slopes);
        return $slopes[floor(count($slopes) / 2)] ?? 0;
    }

    public function luminanceStd(array $colors): float
    {
        $luminances = array_map(fn($c) => 0.299 * $c[0] + 0.587 * $c[1] + 0.114 * $c[2], $colors);
        $std = $this->calculateStdDev($luminances);
        return $this->normalizeFeature($std, 'lum_std');
    }

    public function calculateStdDev(array $values): float
    {
        $variance = $this->calculateVariance($values);
        return sqrt($variance);
    }



    public function calculatePatternMetrics(array $walls): array
    {
        // $left = $walls[1] ?? [];
        // $right = $walls[3] ?? [];

        return [
            // 'vertical_symmetry' => $this->calculateVerticalSymmetry($left, $right),
            // 'horizontal_contrast' => $this->calculateHorizontalContrast($walls),
            // 'depth_contrast' => $this->calculateDepthContrast($walls),
            'pattern_consistency' => $this->calculatePatternConsistency($walls)
        ];
    }


    public function colorDistance(array $c1, array $c2): float
    {
        return sqrt(
            pow($c1[0] - $c2[0], 2) +
                pow($c1[1] - $c2[1], 2) +
                pow($c1[2] - $c2[2], 2)
        );
    }
}
