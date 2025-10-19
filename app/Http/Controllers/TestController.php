<?php

namespace App\Http\Controllers;

use App\Console\Commands\ExportTrainingData;
use App\Models\Chess;
use App\Models\Isa;
use App\Models\RectanglesForResult;
use App\Models\Results;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\FeatureCalculator;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class TestController extends Controller
{
    public function index()
    {
        return view('test.index');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'rectangles' => 'required|array',
                'rectangles.*.color' => 'required|string',
                'rectangles.*.x' => 'required|numeric',
                'rectangles.*.y' => 'required|numeric',
                'rectangles.*.z' => 'required|numeric',
            ]);
            $userId = Auth::id();


            $result = Results::create([
                'user_id' => $userId,
                'isa_id' => null,
                'industry' => null,
                'recommendation' => null,
                'user_image' => $request->svg,
                'match' => null,
            ]);


            foreach ($request->rectangles as $rectangle) {
                RectanglesForResult::create([
                    'result_id' => $result->id,
                    'color' => $rectangle['color'],
                    'x' => $rectangle['x'],
                    'y' => $rectangle['y'],
                    'z' => $rectangle['z']
                ]);
            }


            $this->analyzeResult($result, $request->svg);
            return response()->json(
                ['message' => 'Результат успешно сохранен'],
                200,
                ['Content-Type' => 'application/json; charset=UTF-8'],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
            );
        } catch (Exception $e) {
            return response()->json(
                ['message' => 'Ошибка: ' . $e->getMessage()],
                500,
                ['Content-Type' => 'application/json; charset=UTF-8'],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE
            );
        }
    }


    public function analyzeResult(Results $result, string $svgContent)
    {
        try {
            Log::debug('Raw features data', ['features' => $result->rectanglesForResult]);

            // Создаем временный файл
            $rectangles = $result->rectanglesForResult;

            // Рассчитываем признаки
            $features = FeatureCalculator::calculate($rectangles);
            $chessScore = (new ExportTrainingData())->calculateChessStructureScore(
                $features
            );
            $features['chess_structure'] = $chessScore;
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
            // Формируем команду с передачей путей
            $command = sprintf(
               env('PYTHON_PATH').' "%s" --model_dir "%s" --input "%s" 2>&1',
                str_replace('\\', '/', $pythonScriptPath),
                str_replace('\\', '/', $modelIndustryPath),
                str_replace('\\', '/', $tempFile)
            );

            Log::debug('Executing command', ['command' => $command]);

            $output = [];
            $returnVar = null;
            // exec("{$command}", $output, $returnVar);
            // // exec("{$command} 2>&1", $output, $returnVar);
            // $pythonResponse = implode("\n", $output);
            // Добавьте перед exec()
// Проверим что видит Python при запуске из Apache

            exec("{$command} 2>&1", $output, $returnVar); // Логи ошибок в файл
            // $pythonResponse = end($output);
$pythonResponse = implode("\n", $output);
Log::debug('Full Python output', ['full_output' => $pythonResponse]);
            // Логируем сырой ответ
            Log::debug('Raw Python response', ['response' => $pythonResponse]);

            $response = json_decode($pythonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE || isset($response['error'])) {
                throw new Exception("Ошибка Python: " . ($response['error'] ?? 'Invalid JSON'));
            }

            // Удаляем временный файл
            unlink($tempFile);
            // Логирование полного вывода

            if ($returnVar !== 0) {
                throw new Exception("Python process failed: " . implode("\n", $output));
            }

            // Обработка результатов
            $response = json_decode(implode("\n", $output), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON response from Python");
            }
            $industryName = $response['style_class'];
            $chessStructureName = $response['chess_structure'];


            // $recommendation = $this->generateRecommendation(
            //     $industryName,
            //     $svgContent,
            //     $chessStructureName
            // );

            $result->update([
                // 'industry' => $industryName,
                // 'chess_structure' => $chessStructureName,
                // 'recommendation' => $recommendation
            ]);
            // return $response;

        } catch (Exception $e) {
            Log::error('Temp file status', [
                'exists' => file_exists($tempFile),
                'path' => $tempFile,
                'size' => file_exists($tempFile) ? filesize($tempFile) : 0
            ]);
            throw new Exception("Ошибка анализа данных: " . $e->getMessage());
        }
    }

    // private function generateRecommendation($style, $svg, $chessStructureLevel)
    // {

    //     $result = [
    //         'творец' => [
    //             'сильная' => '<ol><li>Программная инженерия</li><li>Актерское искусство</li><li>Дизайн</li></ol>',
    //             'средняя' => '<ol><li>Программная инженерия</li><li>Архитектура</li><li>Искусство народного пения</li></ol>',
    //             'слабая' => '<ol><li>Веб-дизайн</li><li>Программная инженерия</li><li>Архитектура и строительство</li></ol>'
    //         ],
    //         'авангардист' => [
    //             'сильная' => '<ol><li>Математика и компьютерные науки</li><li>Фундаментальная информатика и информационные технологии</li><li>Фотоника и оптоинформатика</li></ol>',
    //             'средняя' => '<ol><li>Информационные технологии</li><li>Информатика и вычислительная техника</li><li>Медиакоммуникации</li></ol>',
    //             'слабая' => '<ol><li>Бизнес-информатика</li><li>Фундаментальная информатика и информационные технологии</li><li>Математика и компьютерные науки</li><li>Теология</li></ol>'
    //         ],
    //         'смешанный' => [
    //             'сильная' => '<ol><li>Программная инженерия</li><li>Информатика и вычислительная техника</li><li>Прикладная математика</li></ol>',
    //             'средняя' => '<ol><li>Прикладная математика и информатика</li><li>Математика и компьютерные науки</li><li>Фундаментальная информатика и информационные технологии</li></ol>',
    //             'слабая' => '<ol><li>Математика и компьютерные науки</li><li>Программная инженерия</li><li>Информатика и вычислительная техника</li></ol>'
    //         ],
    //         'рационал' => [
    //             'сильная' => '<ol><li>Фундаментальная информатика и информационные технологии</li><li>Физика/Химия</li><li>Фотоника и оптоинформатика</li></ol>',
    //             'средняя' => '<ol><li>Программная инженерия</li><li>Фундаментальная и прикладная химия</li><li>Радиоэлектронные системы и комплексы</li></ol>',
    //             'слабая' => '<ol><li>Математика и компьютерные науки</li><li>Электроника/электротехника</li><li>Безопасность</li></ol>'
    //         ],
    //         'скептик' => [
    //             'сильная' => '<ol><li>Психология</li><li>Социология</li><li>Социальная работа</li></ol>',
    //             'средняя' => '<ol><li>Политология</li><li>Государственное и муниципальное управление</li><li>Бизнес-информатика</li></ol>',
    //             'слабая' => '<ol><li>Юриспруденция</li><li>Правоохранительная деятельность</li><li>Экономическая безопасность</li></ol>'
    //         ]
    //     ];


    //     $response = $result[$style][$chessStructureLevel];

    //     return $response;
    // }
}
