<?php

namespace App\Http\Controllers;

use App\Models\TestingStatistic;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function monthlyStatistics()
    {
        $stats = TestingStatistic::selectRaw('
        DATE_FORMAT(period_date, "%Y-%m") as month,
        SUM(tests_count) as total_tests,
       AVG(IF(tests_count > 0, average_match, NULL)) as avg_match,
        SUM(new_users) as new_users
    ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray(); // Преобразуем в массив

        // Расчёт динамики
        $result = [];
        $base = null;
        $previous = null;

        foreach ($stats as $stat) {
            $current = [
                'month' => $stat['month'],
                'total_tests' => $stat['total_tests'],
                'avg_match' => $stat['avg_match'],
                'new_users' => $stat['new_users']
            ];

            if ($base) {
                // Базисные показатели
                $current['base_abs'] = $current['total_tests'] - $base['total_tests'];
                $current['base_growth'] = $base['total_tests'] > 0
                    ? round(($current['total_tests'] / $base['total_tests']) * 100, 2)
                    : 0;
                $current['base_increase'] = $base['total_tests'] > 0
                    ? round((($current['total_tests'] - $base['total_tests']) / $base['total_tests']) * 100, 2)
                    : 0;
                // Цепные показатели
                $current['chain_abs'] = $current['total_tests'] - $previous['total_tests'];
                $current['chain_growth'] = $previous['total_tests'] > 0
                    ? round(($current['total_tests'] / $previous['total_tests']) * 100, 2)
                    : 0;
                $current['chain_increase'] = $previous['total_tests'] > 0
                    ? round((($current['total_tests'] - $previous['total_tests']) / $previous['total_tests']) * 100, 2)
                    : 0;
            }

            if (!$base) $base = $current;
            $previous = $current;
            $result[] = $current;
        }

        $testsData = array_column($result, 'total_tests');
        $months = array_column($result, 'month');

        $calculateMA = function ($data, $window) {
            $ma = [];
            $n = count($data);
            $p = floor($window / 2);
            for ($i = 0; $i < $n; $i++) {
                if ($i < $p || $i >= $n - $p) {
                    $ma[] = null;
                } else {
                    $sum = array_sum(array_slice($data, $i - $p, $window));
                    $ma[] = round($sum / $window, 2);
                }
            }
            return $ma;
        };
        $moving_average_with_edges = function ($values, $l) {
            $n = count($values);
            $half = floor($l / 2);
            $result = array_fill(0, $n, null);

            // Центральные значения (SMA)
            for ($i = $half; $i < $n - $half; $i++) {
                $sum = 0;
                for ($j = $i - $half; $j <= $i + $half; $j++) {
                    $sum += $values[$j];
                }
                $result[$i] = round($sum / $l, 2);
            }

            // Средний абсолютный прирост по центральным значениям
            $deltas = array();
            for ($i = $half + 1; $i < $n - $half; $i++) {
                if ($result[$i] !== null && $result[$i - 1] !== null) {
                    $deltas[] = $result[$i] - $result[$i - 1];
                }
            }
            $avg_delta = count($deltas) > 0 ? array_sum($deltas) / count($deltas) : 0;

            // Восстановление значений в начале
            for ($i = $half - 1; $i >= 0; $i--) {
                $result[$i] = round($result[$i + 1] - $avg_delta, 2);
            }

            // Восстановление значений в конце
            for ($i = $n - $half; $i < $n; $i++) {
                $result[$i] = round($result[$i - 1] + $avg_delta, 2);
            }

            return $result;
        };
        $ma3 = $calculateMA($testsData, 3);
        $ma5 = $calculateMA($testsData, 5);
        $ma7 = $calculateMA($testsData, 7);

        $ma3_restored = $moving_average_with_edges($testsData, 3);
        $ma5_restored = $moving_average_with_edges($testsData, 5);
        $ma7_restored = $moving_average_with_edges($testsData, 7);



        $calculateForecast = function ($ma, $window) {
            $n = count($ma);
            $p = floor($window / 2);
            $lastIndex = $n - $p - 1;
            if ($lastIndex < 0) return null;

            $activeData = array_slice($ma, $lastIndex - $window + 1, $window);
            $sumGrowth = 0;
            $count = 0;
            for ($i = 1; $i < count($activeData); $i++) {
                if ($activeData[$i] !== null && $activeData[$i - 1] !== null) {
                    $sumGrowth += ($activeData[$i] - $activeData[$i - 1]);
                    $count++;
                }
            }
            $avgGrowth = $count > 0 ? $sumGrowth / $count : 0;
            return round($ma[$lastIndex] + $avgGrowth, 2);
        };

        $forecastMA3 = $calculateForecast($ma3_restored, 3);
        $forecastMA5 = $calculateForecast($ma5_restored, 5);
        $forecastMA7 = $calculateForecast($ma7_restored, 7);

        return view('statistics.monthly', [
            'months' => $months,
            'testsData' => $testsData,
            'matchesData' => array_column($result, 'avg_match'),
            'usersData' => array_column($result, 'new_users'),
            'stats' => $result,
            'ma3_raw' => $ma3,
            'ma5_raw' => $ma5,
            'ma7_raw' => $ma7,

            'ma3' => $ma3_restored,
            'ma5' => $ma5_restored,
            'ma7' => $ma7_restored,
            'forecast' => [
                'ma3' => $forecastMA3,
                'ma5' => $forecastMA5,
                'ma7' => $forecastMA7
            ]
        ]);
    }
    private function checkTrend(array $series)
    {
        $n = count($series);
        if ($n < 3) return ['error' => 'Недостаточно данных для анализа'];

        $sorted = collect($series)->sort()->values()->toArray();
        $median = ($n % 2 == 0)
            ? ($sorted[($n / 2) - 1] + $sorted[$n / 2]) / 2
            : $sorted[floor($n / 2)];

        $signs = collect($series)->map(function ($v) use ($median) {
            return $v > $median ? '+' : ($v < $median ? '-' : '');
        });

        $v = 0;
        $max_len = 0;
        $current_sign = null;
        $current_len = 0;

        foreach ($signs as $sign) {
            if ($sign !== '' && $sign !== $current_sign) {
                $v++;
                $current_sign = $sign;
                $current_len = 1;
            } elseif ($sign === $current_sign) {
                $current_len++;
            }
            if ($current_len > $max_len) $max_len = $current_len;
        }

        $median_test = [
            'median' => $median,
            'signs' => $signs,
            'v' => $v,
            'max_len' => $max_len,
            'left_v' => 0.5 * ($n + 1 - 1.96 * sqrt($n - 1)),
            'right_t' => floor(1.43 * log($n + 1)),
            'has_trend' => $v <= ((0.5 * ($n + 1 - 1.96 * sqrt($n - 1))) ||
                $max_len >= floor(1.43 * log($n + 1)))
        ];

        $signs2 = [];
        for ($i = 1; $i < $n; $i++) {
            if ($series[$i] > $series[$i - 1]) {
                $signs2[] = '+';
            } elseif ($series[$i] < $series[$i - 1]) {
                $signs2[] = '-';
            }
        }
        $m = count($signs2);

        $v2 = 0;
        $max_len2 = 0;
        $current_sign2 = null;
        $current_len2 = 0;

        foreach ($signs2 as $sign) {
            if ($sign !== $current_sign2) {
                $v2++;
                $current_sign2 = $sign;
                $current_len2 = 1;
            } else {
                $current_len2++;
            }
            if ($current_len2 > $max_len2) $max_len2 = $current_len2;
        }

        $direction_test = [
            'signs' => $signs2,
            'v' => $v2,
            'max_len' => $max_len2,
            'left_v' => (1 / 3) * (2 * $n - 1 - 1.96 * sqrt((16 * $n - 29) / 90)),
            'right_t' => $n <= 26 ? 5 : ($n <= 153 ? 6 : 7),
            'has_trend' => $v2 <= (1 / 3) * (2 * $n - 1 - 1.96 * sqrt((16 * $n - 29) / 90)) ||
                $max_len2 >= ($n <= 26 ? 5 : ($n <= 153 ? 6 : 7))
        ];

        return [
            'median_test' => $median_test,
            'direction_test' => $direction_test
        ];
    }

    private function calculateModels(array $series)
    {
        return [
            'linear' => $this->linearModel($series),
            'parabolic' => $this->parabolicModel($series),
            'exponential' => $this->exponentialModel($series)
        ];
    }

    private function linearModel(array $y)
    {
        $n = count($y);
        $sum_t = $n * ($n + 1) / 2;
        $sum_y = array_sum($y);
        $sum_ty = 0;
        $sum_t2 = $n * ($n + 1) * (2 * $n + 1) / 6;

        foreach ($y as $i => $yi) {
            $sum_ty += ($i + 1) * $yi;
        }

        $b = ($n * $sum_ty - $sum_t * $sum_y) / ($n * $sum_t2 - $sum_t ** 2);
        $a = ($sum_y - $b * $sum_t) / $n;

        $predicted = [];
        foreach (range(1, $n) as $t) {
            $predicted[] = $a + $b * $t;
        }

        return [
            'equation' => sprintf('y = %.2f + %.2ft', $a, $b),
            'a' => round($a, 4),
            'b' => round($b, 4),
            'predicted' => $predicted
        ];
    }

    private function parabolicModel(array $y)
    {
        $n = count($y);
        $sum_t = $n * ($n + 1) / 2;
        $sum_t2 = $n * ($n + 1) * (2 * $n + 1) / 6;
        $sum_t3 = pow($n * ($n + 1) / 2, 2);
        $sum_t4 = $n * ($n + 1) * (2 * $n + 1) * (3 * pow($n, 2) + 3 * $n - 1) / 30;

        $sum_y = array_sum($y);
        $sum_ty = 0;
        $sum_t2y = 0;

        foreach ($y as $i => $yi) {
            $t = $i + 1;
            $sum_ty += $t * $yi;
            $sum_t2y += $t * $t * $yi;
        }

        $matrix = [
            [$n, $sum_t, $sum_t2],
            [$sum_t, $sum_t2, $sum_t3],
            [$sum_t2, $sum_t3, $sum_t4]
        ];

        $constants = [$sum_y, $sum_ty, $sum_t2y];

        $det = $this->det3x3($matrix);
        if ($det == 0) return ['error' => 'Система не имеет решения'];

        $det_a = $this->det3x3([
            $constants,
            [$matrix[1][0], $matrix[1][1], $matrix[1][2]],
            [$matrix[2][0], $matrix[2][1], $matrix[2][2]]
        ]);

        $det_b = $this->det3x3([
            [$matrix[0][0], $constants[0], $matrix[0][2]],
            [$matrix[1][0], $constants[1], $matrix[1][2]],
            [$matrix[2][0], $constants[2], $matrix[2][2]]
        ]);

        $det_c = $this->det3x3([
            [$matrix[0][0], $matrix[0][1], $constants[0]],
            [$matrix[1][0], $matrix[1][1], $constants[1]],
            [$matrix[2][0], $matrix[2][1], $constants[2]]
        ]);

        $a = $det_a / $det;
        $b = $det_b / $det;
        $c = $det_c / $det;

        $predicted = [];
        foreach (range(1, $n) as $t) {
            $predicted[] = $a + $b * $t + $c * $t * $t;
        }

        return [
            'equation' => sprintf('y = %.2f + %.2ft + %.2ft²', $a, $b, $c),
            'a' => round($a, 4),
            'b' => round($b, 4),
            'c' => round($c, 4),
            'predicted' => $predicted
        ];
    }

    private function det3x3(array $m)
    {
        return $m[0][0] * ($m[1][1] * $m[2][2] - $m[1][2] * $m[2][1])
            - $m[0][1] * ($m[1][0] * $m[2][2] - $m[1][2] * $m[2][0])
            + $m[0][2] * ($m[1][0] * $m[2][1] - $m[1][1] * $m[2][0]);
    }

    private function exponentialModel(array $y)
    {
        $n = count($y);
        $sum_t = $n * ($n + 1) / 2;
        $sum_ln_y = 0;
        $sum_t_ln_y = 0;

        foreach ($y as $i => $yi) {
            if ($yi <= 0) {
                $ln_yi = 0;
            } else {
                $ln_yi = log($yi);
            }
            $t = $i + 1;
            $sum_ln_y += $ln_yi;
            $sum_t_ln_y += $t * $ln_yi;
        }

        $sum_t2 = $n * ($n + 1) * (2 * $n + 1) / 6;

        $b_ln = ($n * $sum_t_ln_y - $sum_t * $sum_ln_y) / ($n * $sum_t2 - $sum_t ** 2);
        $a_ln = ($sum_ln_y - $b_ln * $sum_t) / $n;

        $a = exp($a_ln);
        $b = exp($b_ln);

        $predicted = [];
        foreach (range(1, $n) as $t) {
            $predicted[] = $a * pow($b, $t);
        }

        return [
            'equation' => sprintf('y = %.2f·%.2f^t', $a, $b),
            'a' => round($a, 4),
            'b' => round($b, 4),
            'predicted' => $predicted
        ];
    }

    public function trendAnalysis()
    {
        $stats = TestingStatistic::selectRaw('
        DATE_FORMAT(period_date, "%Y-%m") as month,
        SUM(tests_count) as total_tests,
        AVG(IF(tests_count > 0, average_match, NULL)) as avg_match,
        SUM(new_users) as new_users
    ')
            ->where('period_date', '<', '2024-03-01')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $series = [
            'tests' => $stats->pluck('total_tests')->toArray(),
            'matches' => $stats->pluck('avg_match')->toArray(),
            'users' => $stats->pluck('new_users')->toArray()
        ];

        $analysis = [];
        foreach ($series as $name => $data) {
            $n = count($data);
            $t = range(1, $n);

            // Расчеты для таблицы
            $yt = [];
            $t2 = [];
            $yt2 = [];
            $t4 = [];
            $lny = [];
            $lnyt = [];
            foreach ($data as $i => $value) {
                $yt[] = $value * $t[$i];
                $t2[] = $t[$i] * $t[$i];
                $yt2[] = $value * $t[$i] * $t[$i];
                $t4[] = $t[$i] * $t[$i] * $t[$i] * $t[$i];
                $lny[] = $value > 0 ? log($value) : 0;
                $lnyt[] = ($value > 0 ? log($value) : 0) * $t[$i];
            }

            $analysis[$name] = [
                'data' => $data,
                'trend' => $this->checkTrend($data),
                'models' => $this->calculateModels($data),
                'calculations' => [
                    't' => $t,
                    'yt' => $yt,
                    't2' => $t2,
                    'yt2' => $yt2,
                    't4' => $t4,
                    'lny' => $lny,
                    'lnyt' => $lnyt
                ]
            ];
        }

        return view('statistics.trend-analysis', [
            'stats' => $stats,
            'analysis' => $analysis,
            'months' => $stats->pluck('month')
        ]);
    }
    public function modelAnalysis()
    {
        $stats = TestingStatistic::selectRaw('
        DATE_FORMAT(period_date, "%Y-%m") as month,
        SUM(tests_count) as total_tests,
        AVG(IF(tests_count > 0, average_match, NULL)) as avg_match,
        SUM(new_users) as new_users
    ')
            ->where('period_date', '<', '2024-03-01')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = $stats->pluck('month');
        $timeVariable = range(1, count($months));

        $analysisResults = [];
        foreach (['total_tests', 'avg_match', 'new_users'] as $series) {
            $actualValues = $stats->pluck($series)->toArray();

            $models = [
                'linear' => $this->linearModel($actualValues),
                'parabolic' => $this->parabolicModel($actualValues),
                'exponential' => $this->exponentialModel($actualValues)
            ];

            $residualAnalysis = [
                'linear' => $this->analyzeResiduals($actualValues, $models['linear']['predicted'], 'linear'),
                'parabolic' => $this->analyzeResiduals($actualValues, $models['parabolic']['predicted'], 'parabolic'),
                'exponential' => $this->analyzeResiduals($actualValues, $models['exponential']['predicted'], 'exponential')
            ];

            $accuracyAnalysis = [
                'linear' => $this->calculateAccuracy($actualValues, $models['linear']['predicted']),
                'parabolic' => $this->calculateAccuracy($actualValues, $models['parabolic']['predicted']),
                'exponential' => $this->calculateAccuracy($actualValues, $models['exponential']['predicted'])
            ];

            $analysisResults[$series] = [
                'actual_values' => $actualValues,
                'models' => $models,
                'residual_analysis' => $residualAnalysis,
                'accuracy_analysis' => $accuracyAnalysis
            ];
        }

        return view('statistics.model-analysis', [
            'months' => $months,
            'timeVariable' => $timeVariable,
            'analysisResults' => $analysisResults,
            'controller' => $this
        ]);
    }

    private function analyzeResiduals($actualValues, $predictedValues, $modelName)
    {
        $residuals = [];
        foreach ($actualValues as $i => $value) {
            $residuals[] = $value - $predictedValues[$i];
        }

        return [
            'signTest' => $this->performSignTest($residuals),
            'normalityTest' => $this->checkNormality($residuals),
            'durbinWatsonTest' => $this->performDurbinWatsonTest($residuals),
            'residuals' => $residuals,
            'modelName' => $modelName
        ];
    }

    private function performSignTest($residuals)
    {
        $n_plus = 0; 
        $n_minus = 0;
        $S = 0;      
        $prev_sign = null;

        foreach ($residuals as $e) {
            $current_sign = $e <=> 0;

            if ($current_sign === 1) $n_plus++;
            if ($current_sign === -1) $n_minus++;

            if ($current_sign !== 0 && $current_sign !== $prev_sign) {
                $S++;
                $prev_sign = $current_sign;
            }
        }

        $n = $n_plus + $n_minus;

        $mu = (2 * $n_plus * $n_minus) / $n + 1;
        $sigma = sqrt(
            (2 * $n_plus * $n_minus * (2 * $n_plus * $n_minus - $n))
                / ($n * $n * ($n - 1))
        );
        $z = ($S - $mu) / $sigma;

        return [
            'positive' => $n_plus,
            'negative' => $n_minus,
            'S' => $S,
            'zValue' => $z,
            'isRandom' => abs($z) < 1.96 
        ];
    }

    private function checkNormality($residuals)
    {
        $n = count($residuals);
        $sum_e2 = array_sum(array_map(function ($e) {
            return pow($e, 2);
        }, $residuals));
        $sum_e3 = array_sum(array_map(function ($e) {
            return pow($e, 3);
        }, $residuals));
        $sum_e4 = array_sum(array_map(function ($e) {
            return pow($e, 4);
        }, $residuals));

        $A = ($sum_e3 / $n) / pow($sum_e2 / $n, 1.5);
        $E = ($sum_e4 / $n) / pow($sum_e2 / $n, 2) - 3;

        $criticalA1 = 1.5 * sqrt((6 * ($n - 2)) / (($n + 1) * ($n + 3)));
        $criticalA2 = 2 * sqrt((6 * ($n - 2)) / (($n + 1) * ($n + 3)));

        $termE = 6 / ($n + 1);
        $denominatorE = pow($n + 1, 2) * ($n + 3) * ($n + 5);
        $criticalE1 = 1.5 * sqrt((24 * $n * ($n - 2) * ($n - 3)) / $denominatorE);
        $criticalE2 = 2 * sqrt((24 * $n * ($n - 2) * ($n - 3)) / $denominatorE);

        $normalityConclusion = "Требуется дополнительная проверка";
        $conclusionClass = "text-warning"; 

        if (abs($A) < $criticalA1 && abs($E + $termE) < $criticalE1) {
            $normalityConclusion = "Нормальное распределение (не отвергается)";
            $conclusionClass = "text-success";
        } elseif (abs($A) >= $criticalA2 || abs($E + $termE) >= $criticalE2) {
            $normalityConclusion = "Ненормальное распределение (отвергается)";
            $conclusionClass = "text-danger";
        }

        return [
             'conclusionClass' => $conclusionClass,
            'skewness' => $A,
            'kurtosis' => $E,
            'isNormal' => $normalityConclusion === "Нормальное распределение (не отвергается)",
            'normalityConclusion' => $normalityConclusion,
            'criticalValues' => [
                'A' => ['criticalA1' => $criticalA1, 'criticalA2' => $criticalA2],
                'E' => ['criticalE1' => $criticalE1, 'criticalE2' => $criticalE2, 'termE' => $termE]
            ]
        ];
    }

    private function performDurbinWatsonTest($residuals)
    {
        $numerator = 0;
        for ($i = 1; $i < count($residuals); $i++) {
            $numerator += pow($residuals[$i] - $residuals[$i - 1], 2);
        }

        $denominator = array_sum(array_map(function ($e) {
            return pow($e, 2);
        }, $residuals));
        $d = $numerator / $denominator;

        $conclusion = "Автокорреляция отсутствует";
        if ($d < 1.08) $conclusion = "Положительная автокорреляция";
        elseif ($d > 1.36) $conclusion = "Отрицательная автокорреляция";

        return [
            'dStatistic' => $d,
            'conclusion' => $conclusion
        ];
    }

    private function calculateAccuracy($actual, $predicted)
    {
        $n = count($actual);
        $sse = 0;
        $apeSum = 0;

        for ($i = 0; $i < $n; $i++) {
            $error = $actual[$i] - $predicted[$i];
            $sse += pow($error, 2);
            if ($actual[$i] != 0) {
                $apeSum += abs($error / $actual[$i]);
            }
        }

        $mse = $sse / $n;
        $rmse = sqrt($mse);
        $mape = ($apeSum / $n) * 100;

        return [
            'sse' => $sse,
            'mse' => $mse,
            'rmse' => $rmse,
            'mape' => $mape,
            'n' => $n
        ];
    }

    public function buildDurbinWatsonTable($actualValues, $predictedValues, $timeVariable, $modelName)
    {
        $modelTitles = [
            'linear' => 'ЛИНЕЙНОЙ МОДЕЛИ',
            'parabolic' => 'ПАРАБОЛИЧЕСКОЙ МОДЕЛИ',
            'exponential' => 'ПОКАЗАТЕЛЬНОЙ МОДЕЛИ'
        ];

        $modelTitle = $modelTitles[$modelName] ?? 'МОДЕЛИ';

        $residuals = [];
        $squaredResiduals = [];
        $squaredDifferences = [null];

        foreach ($actualValues as $i => $value) {
            $residual = $value - $predictedValues[$i];
            $residuals[] = $residual;
            $squaredResiduals[] = pow($residual, 2);

            if ($i > 0) {
                $squaredDifferences[] = pow($residual - $residuals[$i - 1], 2);
            }
        }

        $sumSquaredResiduals = array_sum($squaredResiduals);
        $sumSquaredDifferences = array_sum(array_slice($squaredDifferences, 1));

        $d = $sumSquaredDifferences / $sumSquaredResiduals;

        return [
            'residuals' => $residuals,
            'squaredResiduals' => $squaredResiduals,
            'squaredDifferences' => $squaredDifferences,
            'sumSquaredResiduals' => $sumSquaredResiduals,
            'sumSquaredDifferences' => $sumSquaredDifferences,
            'dStatistic' => $d,
            'modelTitle' => $modelTitle
        ];
    }



    protected function interpretDurbinWatson($d)
    {
        if ($d < 1.08) return "Положительная автокорреляция";
        if ($d > 1.36) return "Отрицательная автокорреляция";
        return "Автокорреляция отсутствует";
    }
}
