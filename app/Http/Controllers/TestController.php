<?php

namespace App\Http\Controllers;

use App\Console\Commands\ExportTrainingData;
use App\Models\Chess;
use App\Models\Isa;
use App\Models\RectanglesForResult;
use App\Models\Results;
use App\Models\Test;
use App\Models\User;
use App\Models\UserTest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\FeatureCalculator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Services\MailService;

class TestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tests = Test::all();
        $userTests = Results::where('user_id', $user->id)
            ->get()
            ->keyBy('test_id');

        return view('test.index', compact('tests', 'userTests'));
    }

    public function show(Test $test)
    {
        return view('test.' . $test->code, ['testId' => $test->id]);
        // switch ($test->code) {
        //     case 'color_test':
        //         return view('test.colortest', ['testId' => $test->id]);
        //     case 'holland':
        //         return view()
        // }
    }

    public function save(Test $test, Request $request)
    {
        DB::beginTransaction();

        try {
            switch ($test->code) {
                case 'color_test':
                    $result = $this->saveColorTest($request);
                    break;
                case 'holland':
                    $result = $this->saveHolland($request);
                    break;
                case 'interest_map':
                    $result = $this->saveInterestMap($request);
                    break;
                case 'ddo':
                    $result = $this->saveDDO($request);
                    break;
                case 'jovayshi':
                    $result = $this->saveJovayshi($request);
                    break;
                case 'profession_matrix':
                    $result = $this->saveProfessionMatrix($request);
                    break;
                case 'eisenck_state':
                    $result = $this->saveEisenckState($request);
                    break;
                case 'jung_character':
                    $result = $this->saveJungCharacter($request);
                    break;
                case 'thomas_conflict':
                    $result = $this->saveThomasConflict($request);
                    break;
                case 'keirsey':
                    $result = $this->saveKeirsey($request);
                    break;
                case 'leader_organizer':
                    $result = $this->saveLeaderOrganizer($request);
                    break;
                case 'self_development':
                    $result = $this->saveSelfDevelopment($request);
                    break;
            }

            if ($result) {
                $result->update([
                    'test_id' => $test->id
                ]);
                $notificationResult = $this->sendTestCompletionNotifications($test, $result);
            }

            DB::commit();
            return response()->json(
                [
                    'message' => 'Результат успешно сохранен',
                    'notification' => $notificationResult['user'] ?? null
                ],
                200,
                ['Content-Type' => 'application/json; charset=UTF-8'],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                ['message' => 'Ошибка: ' . $e->getMessage()],
                500,
                ['Content-Type' => 'application/json; charset=UTF-8'],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
            );
        }
    }

    private function saveColorTest(Request $request)
    {
        // try {
        $validated = $request->validate([
            'rectangles' => 'required|array',
            'rectangles.*.color' => 'required|string',
            'rectangles.*.x' => 'required|numeric',
            'rectangles.*.y' => 'required|numeric',
            'rectangles.*.z' => 'required|numeric',
            'svg' => 'required|string',
        ]);
        $userId = Auth::id();

        $result = Results::create([
            'user_id' => $userId,
            'user_image' => $validated['svg'],
        ]);

        foreach ($validated['rectangles'] as $rectangle) {
            RectanglesForResult::create([
                'result_id' => $result->id,
                'color' => $rectangle['color'],
                'x' => $rectangle['x'],
                'y' => $rectangle['y'],
                'z' => $rectangle['z']
            ]);
        }


        return $this->analyzeResult($result, $validated['svg']);

        // return response()->json(
        //     ['message' => 'Результат успешно сохранен'],
        //     200,
        //     ['Content-Type' => 'application/json; charset=UTF-8'],
        //     JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
        // );
        // } catch (Exception $e) {
        // return response()->json(
        //     ['message' => 'Ошибка: ' . $e->getMessage()],
        //     500,
        //     ['Content-Type' => 'application/json; charset=UTF-8'],
        //     JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
        // );
        // }
    }


    private function analyzeResult(Results $result, string $svgContent)
    {
        // try {
        Log::debug('Raw features data', ['features' => $result->rectanglesForResult]);

        $rectangles = $result->rectanglesForResult;

        $features = FeatureCalculator::calculate($rectangles);
        // $chessScore = (new ExportTrainingData())->calculateChessStructureScore(
        //     $features
        // );
        // $features['chess_structure'] = $chessScore;
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception("Cannot create temp directory: " . $tempDir);
            }
        }
        if (!is_writable($tempDir)) {
            throw new Exception("Temp directory is not writable: " . $tempDir);
        }
        $tempFile = $tempDir . '/ml_data_' . uniqid() . '.json';
        file_put_contents($tempFile, json_encode($features));
        Log::debug('Temp file content', [
            'content' => file_get_contents($tempFile),
            'size' => filesize($tempFile)
        ]);

        $pythonScriptPath = base_path('app/Services/predict.py');
        $modelIndustryPath = base_path('app/Services');
        if (!file_exists($tempFile)) {
            throw new Exception("Temp file not created: " . $tempFile);
        }
        $command = sprintf(
            env('PYTHON_PATH') . ' "%s" --model_dir "%s" --input "%s" 2>&1',
            str_replace('\\', '/', $pythonScriptPath),
            str_replace('\\', '/', $modelIndustryPath),
            str_replace('\\', '/', $tempFile)
        );

        Log::debug('Executing command', ['command' => $command]);

        $output = [];
        $returnVar = null;

        exec("{$command} 2>&1", $output, $returnVar);
        $pythonResponse = implode("\n", $output);
        Log::debug('Full Python output', ['full_output' => $pythonResponse]);
        Log::debug('Raw Python response', ['response' => $pythonResponse]);

        $response = json_decode($pythonResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE || isset($response['error'])) {
            throw new Exception("Ошибка Python: " . ($response['error'] ?? 'Invalid JSON'));
        }

        unlink($tempFile);

        if ($returnVar !== 0) {
            throw new Exception("Python process failed: " . implode("\n", $output));
        }

        $response = json_decode(implode("\n", $output), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from Python");
        }
        $educationProgram = $response['education_program'];
        $topPredictions = $educationProgram['top_k'] ?? [];
        // $chessStructureName = $response['chess_structure'];

        Log::debug('Python predictions raw', [
            'predictions' => $topPredictions,
            'count' => count($topPredictions),
            'first_item' => $topPredictions[0] ?? null
        ]);

        $result->ml_predictions = $topPredictions;

        $result->save();

        return $result;
        // return [
        //     'predictions' => $topPredictions,
        //     'raw_response' => $response
        // ];
        // } catch (Exception $e) {
        //     Log::error('Temp file status', [
        //         'exists' => file_exists($tempFile),
        //         'path' => $tempFile,
        //         'size' => file_exists($tempFile) ? filesize($tempFile) : 0
        //     ]);
        //     if ($tempFile && file_exists($tempFile)) {
        //         unlink($tempFile);
        //     }
        //     throw new Exception("Ошибка анализа данных: " . $e->getMessage());
        // }
    }

    private function saveHolland(Request $request)
    {
        $answers = $request['answers'];

        $types = [
            // Реалистический тип (15)
            'R' => ['1A', '2A', '3A', '4A', '5A', '16A', '17A', '18A', '19A', '20A', '31A', '32A', '33A', '34A', '35A'],

            // Интеллектуальный тип (15)
            'I' => ['1B', '6A', '7A', '8A', '9A', '16B', '21A', '22A', '23A', '24A', '31B', '36A', '37A', '38A', '39A'],

            // Социальный тип (15)
            'S' => ['2B', '6B', '10A', '11A', '12A', '17B', '21B', '25A', '26A', '27A', '32B', '36B', '40A', '41A', '42A'],

            // Конвенциальный тип (14)
            'C' => ['3B', '7B', '10B', '13A', '14A', '18B', '22B', '25B', '28A', '29A', '33B', '37B', '40B', '43A'],

            // Предприимчивый тип (14)
            'E' => ['4B', '8B', '11B', '13B', '15A', '19B', '23B', '26B', '28B', '30A', '34B', '38B', '41B', '43B'],

            // Артистичный тип (13)
            'A' => ['5B', '9B', '12B', '14B', '15B', '20B', '24B', '27B', '29B', '30B', '35B', '39B', '42B'],
        ];

        $scores = [
            'R' => 0,
            'I' => 0,
            'S' => 0,
            'C' => 0,
            'E' => 0,
            'A' => 0,
        ];

        foreach ($answers as $question => $answer) {
            $answerKey = $question . $answer;

            foreach ($types as $type => $matches) {
                if (in_array($answerKey, $matches)) {
                    $scores[$type]++;
                }
            }
        }
        $normalized = [];

        foreach ($scores as $type => $score) {
            if (count($types[$type]) > 0) {
                $normalized[$type] = round(($score / count($types[$type])) * 100, 2);
            } else {
                $normalized[$type] = 0;
            }
        }

        arsort($normalized);
        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode($normalized)
        ]);
        return $result;
    }

    private function saveInterestMap(Request $request)
    {
        $answers = $request['answers'];

        // Ключ: каждый вопрос относится к одной из 16 шкал
        $scales = [
            'физика'          => [1, 17, 33, 49, 65, 81],
            'математика'      => [2, 18, 34, 50, 66, 82],
            'электронная радиотехника' => [3, 19, 35, 51, 67, 83],
            'техника'         => [4, 20, 36, 52, 68, 84],
            'химия'           => [5, 21, 37, 53, 69, 85],
            'биология'        => [6, 22, 38, 54, 70, 86],
            'медицина'        => [7, 23, 39, 55, 71, 87],
            'география и геология' => [8, 24, 40, 56, 72, 88],
            'история'         => [9, 25, 41, 57, 73, 89],
            'филология и журналистика' => [10, 26, 42, 58, 74, 90],
            'искусство'       => [11, 27, 43, 59, 75, 91],
            'педагогика'      => [12, 28, 44, 60, 76, 92],
            'сфера бытового обслуживания' => [13, 29, 45, 61, 77, 93],
            'военное дело'    => [14, 30, 46, 62, 78, 94],
            'спорт'           => [15, 31, 47, 63, 79, 95],
            'предпринимательство, бизнес' => [16, 32, 48, 64, 80, 96],
        ];

        // Баллы для ответов
        $scores = [
            '++' => 2,
            '+'  => 1,
            '0'  => 0,
            '-'  => -1,
            '--' => -2,
        ];

        $results = [];
        foreach ($scales as $scale => $questions) {
            $score = 0;
            foreach ($questions as $question) {
                if (isset($answers[$question]) && isset($scores[$answers[$question]])) {
                    $score += $scores[$answers[$question]];
                }
            }
            $results[$scale] = $score;
        }

        arsort($results); // сортировка по убыванию

        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode($results, JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveDDO(Request $request)
    {
        $answers = $request['answers'];

        $types = [
            'Человек-природа'      => ['1A', '3B', '6A', '10A', '11A', '13B', '16A', '20A'],
            'Человек-техника'      => ['1B', '4A', '7B', '9A', '11B', '14A', '17B', '19A'],
            'Человек-человек'      => ['2A', '4B', '6B', '8A', '12A', '14B', '16B', '18A'],
            'Человек-знаковая система' => ['2B', '5A', '9B', '10B', '12B', '15A', '19B', '20B'],
            'Человек-художественный образ' => ['3A', '5B', '7A', '8B', '13A', '15B', '17A', '18B'],
        ];

        $scores = [
            'Человек-природа' => 0,
            'Человек-техника' => 0,
            'Человек-человек' => 0,
            'Человек-знаковая система' => 0,
            'Человек-художественный образ' => 0,
        ];

        foreach ($answers as $question => $answer) {
            $answerKey = $question . $answer;

            foreach ($types as $type => $matches) {
                if (in_array($answerKey, $matches)) {
                    $scores[$type]++;
                }
            }
        }


        arsort($scores);

        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode($scores, JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveJovayshi(Request $request)
    {
        $answers = $request['answers'];
        $key = [
            1 => ['a' => 'I', 'b' => 'IV', 'c' => 'VI'],
            2 => ['a' => 'II', 'b' => 'IV', 'c' => 'V'],
            3 => ['a' => 'I', 'b' => 'II', 'c' => 'IV'],
            4 => ['a' => 'III', 'b' => 'V', 'c' => 'VI'],
            5 => ['a' => 'I', 'b' => 'II', 'c' => 'III'],
            6 => ['a' => 'I', 'b' => 'II', 'c' => 'VI'],
            7 => ['a' => 'II', 'b' => 'III', 'c' => 'IV'],
            8 => ['a' => 'I', 'b' => 'V', 'c' => 'VI'],
            9 => ['a' => 'II', 'b' => 'IV', 'c' => 'V'],
            10 => ['a' => 'IV', 'b' => 'V', 'c' => 'VI'],
            11 => ['a' => 'I', 'b' => 'II', 'c' => 'III'],
            12 => ['a' => 'III', 'b' => 'IV', 'c' => 'V'],
            13 => ['a' => 'I', 'b' => 'V', 'c' => 'VI'],
            14 => ['a' => 'II', 'b' => 'IV', 'c' => 'V'],
            15 => ['a' => 'I', 'b' => 'III', 'c' => 'V'],
            16 => ['a' => 'I', 'b' => 'II', 'c' => 'VI'],
            17 => ['a' => 'IV', 'b' => 'V', 'c' => 'VI'],
            18 => ['a' => 'I', 'b' => 'II', 'c' => 'III'],
            19 => ['a' => 'III', 'b' => 'V', 'c' => 'VI'],
            20 => ['a' => 'I', 'b' => 'III', 'c' => 'VI'],
            21 => ['a' => 'II', 'b' => 'III', 'c' => 'IV'],
            22 => ['a' => 'II', 'b' => 'III', 'c' => 'IV'],
            23 => ['a' => 'II', 'b' => 'IV', 'c' => 'VI'],
            24 => ['a' => 'I', 'b' => 'V', 'c' => 'VI'],
        ];

        $scores = [
            'I' => 0, // Склонность к работе с людьми
            'II' => 0, // Склонность к исследовательской работе
            'III' => 0, // Склонность к практической деятельности
            'IV' => 0, // Склонность к эстетическим видам деятельности
            'V' => 0, // Склонность к экстремальным видам деятельности
            'VI' => 0, // Склонность к планово-экономическим видам деятельности
        ];

        foreach ($answers as $question => $answer) {
            if (isset($key[$question][$answer])) {
                $type = $key[$question][$answer];
                $scores[$type]++;
            }
        }
        arsort($scores);

        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode($scores, JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveProfessionMatrix(Request $request)
    {
        $answers = $request['answers'];
        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode($answers, JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveEisenckState(Request $request)
    {
        $answers = $request['answers'];

        $scores = [
            'тревожность' => 0,    // вопросы 1-10
            'фрустрация' => 0,     // вопросы 11-20
            'агрессивность' => 0,  // вопросы 21-30
            'ригидность' => 0,     // вопросы 31-40
        ];

        for ($i = 1; $i <= 10; $i++) {
            if (isset($answers[$i])) {
                $scores['тревожность'] += (int)$answers[$i];
            }
        }

        for ($i = 11; $i <= 20; $i++) {
            if (isset($answers[$i])) {
                $scores['фрустрация'] += (int)$answers[$i];
            }
        }

        for ($i = 21; $i <= 30; $i++) {
            if (isset($answers[$i])) {
                $scores['агрессивность'] += (int)$answers[$i];
            }
        }

        for ($i = 31; $i <= 40; $i++) {
            if (isset($answers[$i])) {
                $scores['ригидность'] += (int)$answers[$i];
            }
        }

        // Добавляем интерпретацию
        $interpretation = [];
        foreach ($scores as $scale => $score) {
            if ($score <= 7) {
                $level = 'низкий';
            } elseif ($score <= 14) {
                $level = 'средний';
            } else {
                $level = 'высокий';
            }
            $interpretation[$scale] = [
                'score' => $score,
                'level' => $level,
                'percent' => round(($score / 20) * 100, 2)
            ];
        }

        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode([
                'scores' => $scores,
                'interpretation' => $interpretation
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveJungCharacter(Request $request)
    {
        $answers = $request['answers'];

        // Ключ для экстраверсии (из документации)
        $extrovertKeys = [
            1 => 'b',
            2 => 'a',
            3 => 'b',
            5 => 'a',
            6 => 'b',
            7 => 'a',
            8 => 'b',
            9 => 'a',
            10 => 'b',
            11 => 'a',
            12 => 'b',
            13 => 'a',
            14 => 'b',
            15 => 'a',
            16 => 'a',
            17 => 'a',
            18 => 'a',
            19 => 'b',
            20 => 'a'
        ];

        $extrovertCount = 0;

        foreach ($answers as $question => $answer) {
            $questionNum = (int)$question;
            if (isset($extrovertKeys[$questionNum]) && $answer === $extrovertKeys[$questionNum]) {
                $extrovertCount++;
            }
        }

        $score = $extrovertCount * 5;

        if ($score <= 35) {
            $type = 'интроверт';
        } elseif ($score <= 65) {
            $type = 'амбиверт';
        } else {
            $type = 'экстраверт';
        }


        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode([
                'score' => $score,
                'type' => $type,
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveThomasConflict(Request $request)
    {
        $answers = $request['answers'];

        $keys = [
            'соперничество' => ['3A', '6B', '8A', '9B', '10A', '13B', '14B', '16B', '17A', '22B', '25A', '28A'],
            'сотрудничество' => ['2B', '5A', '8B', '11A', '14A', '19A', '20A', '21B', '23B', '26B', '28B', '30B'],
            'компромисс' => ['2A', '4A', '7B', '10B', '12B', '13A', '18B', '22A', '23A', '24B', '26A', '29A'],
            'избегание' => ['1A', '5B', '6A', '7A', '9A', '12A', '15B', '17B', '19B', '20B', '27A', '29B'],
            'приспособление' => ['1B', '3B', '4B', '11B', '15A', '16A', '18A', '21A', '24A', '25B', '27B', '30A'],
        ];

        $scores = [
            'соперничество' => 0,
            'сотрудничество' => 0,
            'компромисс' => 0,
            'избегание' => 0,
            'приспособление' => 0,
        ];

        foreach ($answers as $questionNum => $answer) {
            $answerKey = $questionNum . $answer;

            foreach ($keys as $type => $typeKeys) {
                if (in_array($answerKey, $typeKeys)) {
                    $scores[$type]++;
                    break;
                }
            }
        }

        arsort($scores);

        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode([
                'scores' => $scores,
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveKeirsey(Request $request)
    {
        $answers = $request['answers'];

        $scales = [
            // Интроверсия/Экстраверсия (вопросы 1-10)
            'экстраверсия' => [1 => 'a', 2 => 'a', 3 => 'a', 4 => 'a', 5 => 'a', 6 => 'a', 7 => 'a', 8 => 'a', 9 => 'a', 10 => 'a'],
            'интроверсия' => [1 => 'b', 2 => 'b', 3 => 'b', 4 => 'b', 5 => 'b', 6 => 'b', 7 => 'b', 8 => 'b', 9 => 'b', 10 => 'b'],

            // Сенсорика/Интуиция (вопросы 11-30)
            'сенсорика' => [11 => 'a', 12 => 'a', 13 => 'a', 14 => 'a', 15 => 'a', 16 => 'a', 17 => 'a', 18 => 'a', 19 => 'a', 20 => 'a', 21 => 'a', 22 => 'a', 23 => 'a', 24 => 'a', 25 => 'a', 26 => 'a', 27 => 'a', 28 => 'a', 29 => 'a', 30 => 'a'],
            'интуиция' => [11 => 'b', 12 => 'b', 13 => 'b', 14 => 'b', 15 => 'b', 16 => 'b', 17 => 'b', 18 => 'b', 19 => 'b', 20 => 'b', 21 => 'b', 22 => 'b', 23 => 'b', 24 => 'b', 25 => 'b', 26 => 'b', 27 => 'b', 28 => 'b', 29 => 'b', 30 => 'b'],

            // Логика/Этика (вопросы 31-50)
            'логика' => [31 => 'a', 32 => 'a', 33 => 'a', 34 => 'a', 35 => 'a', 36 => 'a', 37 => 'a', 38 => 'a', 39 => 'a', 40 => 'a', 41 => 'a', 42 => 'a', 43 => 'a', 44 => 'a', 45 => 'a', 46 => 'a', 47 => 'a', 48 => 'a', 49 => 'a', 50 => 'a'],
            'этика' => [31 => 'b', 32 => 'b', 33 => 'b', 34 => 'b', 35 => 'b', 36 => 'b', 37 => 'b', 38 => 'b', 39 => 'b', 40 => 'b', 41 => 'b', 42 => 'b', 43 => 'b', 44 => 'b', 45 => 'b', 46 => 'b', 47 => 'b', 48 => 'b', 49 => 'b', 50 => 'b'],

            // Рациональность/Иррациональность (вопросы 51-70)
            'рациональность' => [51 => 'a', 52 => 'a', 53 => 'a', 54 => 'a', 55 => 'a', 56 => 'a', 57 => 'a', 58 => 'a', 59 => 'a', 60 => 'a', 61 => 'a', 62 => 'a', 63 => 'a', 64 => 'a', 65 => 'a', 66 => 'a', 67 => 'a', 68 => 'a', 69 => 'a', 70 => 'a'],
            'иррациональность' => [51 => 'b', 52 => 'b', 53 => 'b', 54 => 'b', 55 => 'b', 56 => 'b', 57 => 'b', 58 => 'b', 59 => 'b', 60 => 'b', 61 => 'b', 62 => 'b', 63 => 'b', 64 => 'b', 65 => 'b', 66 => 'b', 67 => 'b', 68 => 'b', 69 => 'b', 70 => 'b'],
        ];

        // Инициализация счетчиков
        $scores = [
            'экстраверсия' => 0,
            'интроверсия' => 0,
            'сенсорика' => 0,
            'интуиция' => 0,
            'логика' => 0,
            'этика' => 0,
            'рациональность' => 0,
            'иррациональность' => 0,
        ];

        // Подсчет баллов
        foreach ($answers as $question => $answer) {
            $questionNum = (int)$question;

            foreach ($scales as $scale => $scaleKeys) {
                if (isset($scaleKeys[$questionNum]) && $answer === $scaleKeys[$questionNum]) {
                    $scores[$scale]++;
                    break;
                }
            }
        }

        // Определение типа личности
        $type = '';
        $type .= ($scores['экстраверсия'] > $scores['интроверсия']) ? 'E' : 'I';
        $type .= ($scores['сенсорика'] > $scores['интуиция']) ? 'S' : 'N';
        $type .= ($scores['логика'] > $scores['этика']) ? 'T' : 'F';
        $type .= ($scores['рациональность'] > $scores['иррациональность']) ? 'J' : 'P';

        // Соционический тип (упрощенно)
        $socionicTypes = [
            'ISTJ' => 'Максим (Систематик)',
            'ISFJ' => 'Драйзер (Хранитель)',
            'INFJ' => 'Достоевский (Гуманист)',
            'INTJ' => 'Робеспьер (Аналитик)',
            'ISTP' => 'Габен (Мастер)',
            'ISFP' => 'Дюма (Посредник)',
            'INFP' => 'Есенин (Лирик)',
            'INTP' => 'Бальзак (Критик)',
            'ESTP' => 'Жуков (Организатор)',
            'ESFP' => 'Наполеон (Лидер)',
            'ENFP' => 'Гексли (Инициатор)',
            'ENTP' => 'Дон Кихот (Искатель)',
            'ESTJ' => 'Штирлиц (Администратор)',
            'ESFJ' => 'Гюго (Энтузиаст)',
            'ENFJ' => 'Гамлет (Артист)',
            'ENTJ' => 'Джек (Предприниматель)',
        ];

        $result = [
            'scores' => $scores,
            'personality_type' => $type,
            'socionic_name' => $socionicTypes[$type] ?? 'Не определен',
        ];

        $userId = Auth::id();
        $savedResult = Results::create([
            'user_id' => $userId,
            'data' => json_encode($result, JSON_UNESCAPED_UNICODE)
        ]);

        return $savedResult;
    }

    private function saveLeaderOrganizer(Request $request)
    {
        $answers = $request['answers'];

        $key = [
            1 => 'a',
            2 => 'a',
            3 => 'b',
            4 => 'a',
            5 => 'a',
            6 => 'b',
            7 => 'a',
            8 => 'b',
            9 => 'b',
            10 => 'a',
            11 => 'a',
            12 => 'a',
            13 => 'b',
            14 => 'b',
            15 => 'a',
            16 => 'b',
            17 => 'a',
            18 => 'b',
            19 => 'b',
            20 => 'a',
            21 => 'a',
            22 => 'a',
            23 => 'a',
            24 => 'a',
            25 => 'b',
            26 => 'a',
            27 => 'b',
            28 => 'a',
            29 => 'b',
            30 => 'b',
            31 => 'a',
            32 => 'a',
            33 => 'b',
            34 => 'a',
            35 => 'b',
            36 => 'b',
            37 => 'a',
            38 => 'b',
            39 => 'a',
            40 => 'b',
            41 => 'a',
            42 => 'a',
            43 => 'a',
            44 => 'a',
            45 => 'b',
            46 => 'a',
            47 => 'b',
            48 => 'a',
            49 => 'b',
            50 => 'b'
        ];

        $score = 0;
        foreach ($answers as $question => $answer) {
            $questionNum = (int)$question;
            if (isset($key[$questionNum]) && $answer === $key[$questionNum]) {
                $score++;
            }
        }

        $percentage = round(($score / 50) * 100);

        $level = '';

        if ($score < 25) {
            $level = 'Слабый';
        } elseif ($score >= 26 && $score <= 35) {
            $level = 'Средний';
        } elseif ($score >= 36 && $score <= 40) {
            $level = 'Сильный';
        } elseif ($score > 40) {
            $level = 'Очень сильный (склонность к диктату)';
        }




        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode([
                'score' => $score,
                'percentage' => $percentage,
                'level' => $level,
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function saveSelfDevelopment(Request $request)
    {
        $answers = $request['answers'];
        Log::debug($answers);
        $key = [
            1 => ['a' => 2, 'b' => 1, 'c' => 3],
            2 => ['a' => 3, 'b' => 2, 'c' => 1],
            3 => ['a' => 3, 'b' => 2, 'c' => 1],
            4 => ['a' => 2, 'b' => 3, 'c' => 1],
            5 => ['a' => 3, 'b' => 2, 'c' => 2],
            6 => ['a' => 2, 'b' => 3, 'c' => 1],
            7 => ['a' => 3, 'b' => 2, 'c' => 1],
            8 => ['a' => 2, 'b' => 3, 'c' => 1],
            9 => ['a' => 2, 'b' => 3, 'c' => 1],
            10 => ['a' => 1, 'b' => 2, 'c' => 3],
            11 => ['a' => 1, 'b' => 3, 'c' => 2],
            12 => ['a' => 3, 'b' => 2, 'c' => 1],
            13 => ['a' => 1, 'b' => 3, 'c' => 2],
            14 => ['a' => 1, 'b' => 3, 'c' => 2],
            15 => ['a' => 3, 'b' => 2, 'c' => 1],
            16 => ['a' => 2, 'b' => 1, 'c' => 3],
            17 => ['a' => 2, 'b' => 3, 'c' => 1],
        ];

        $score = 0;
        foreach ($answers as $question => $answer) {
            $questionNum = (int)$question;
            if (isset($key[$questionNum][$answer])) {
                $score += $key[$questionNum][$answer];
            }
        }

        $percentage = round(($score / 51) * 100);

        $level = '';
        $levelName = '';

        if ($score >= 18 && $score <= 25) {
            $level = 1;
            $levelName = 'Очень низкий';
        } elseif ($score >= 26 && $score <= 28) {
            $level = 2;
            $levelName = 'Низкий';
        } elseif ($score >= 29 && $score <= 31) {
            $level = 3;
            $levelName = 'Ниже среднего';
        } elseif ($score >= 32 && $score <= 34) {
            $level = 4;
            $levelName = 'Чуть ниже среднего';
        } elseif ($score >= 35 && $score <= 37) {
            $level = 5;
            $levelName = 'Средний';
        } elseif ($score >= 38 && $score <= 40) {
            $level = 6;
            $levelName = 'Чуть выше среднего';
        } elseif ($score >= 41 && $score <= 43) {
            $level = 7;
            $levelName = 'Выше среднего';
        } elseif ($score >= 44 && $score <= 46) {
            $level = 8;
            $levelName = 'Высокий';
        } elseif ($score >= 47 && $score <= 54) {
            $level = 9;
            $levelName = 'Очень высокий';
        }


        $userId = Auth::id();
        $result = Results::create([
            'user_id' => $userId,
            'data' => json_encode([
                'score' => $score,
                'percentage' => $percentage,
                'level' => $level,
                'level_name' => $levelName,
            ], JSON_UNESCAPED_UNICODE)
        ]);

        return $result;
    }

    private function sendTestCompletionNotifications($test, $result)
    {
        try {
            $user = Auth::user();
            $mailService = new MailService();
            $notifications = [
                'user' => null,
                'admins' => []
            ];
            // Получаем email'ы администраторов (пользователи с типом admin)
            $adminEmails = User::where('isAdmin', '1')
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            // Отправляем уведомление пользователю
            if (!empty($user->email)) {
                $notifications['user'] = $mailService->sendTestCompletedNotification(
                    $user->email,
                    $user->login ?? $user->email,
                    $test->name
                );

            }

            // Отправляем уведомления администраторам
            if (!empty($adminEmails)) {
                $notifications['admins'] = $mailService->sendTestCompletedToAdmins(
                    $user->login ?? $user->email,
                    $user->email ?? 'не указан',
                    $test->name,
                    $adminEmails
                );
            }
            return $notifications;
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ошибка отправки уведомлений: ' . $e->getMessage());
        }
    }
}
