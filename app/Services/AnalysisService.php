<?php

namespace App\Services;

use App\Models\User;
use App\Models\Test;
use App\Models\Results;
use App\Services\PdfService;
use Illuminate\Support\Facades\Log;

class AnalysisService
{

    public function streamAnalysisPdf(User $user)
    {
        $analysis = $this->getFullAnalysis($user);
        $pdfService = new PdfService();
        return $pdfService->stream('pdf.report', [
            'analysis' => $analysis
        ], 'analysis_' . $user->id . '.pdf');
    }

    public function showAnalysis(User $user)
    {
        $analysis = $this->getFullAnalysis($user);
        return view('pdf.report', [
            'analysis' => $analysis
        ]);
    }
    public function getFullAnalysis(User $user): array
    {
        // Получаем все результаты без группировки
        $allResults = $user->results()->get();

        $analysis = [
            'user' => [
                'id' => $user->id,
                'login' => $user->login,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'generated_at' => now()->format('d.m.Y H:i'),
            'tests' => []
        ];

        // Группируем вручную, заменяя null на 13
        $grouped = [];
        foreach ($allResults as $result) {
            $testId = $result->test_id ?? 13;
            $grouped[$testId][] = $result;
        }

        foreach ($grouped as $testId => $userResults) {
            // Ищем тест по ID
            $test = Test::find($testId);

            if (!$test) {
                //Log::warning('Тест не найден', ['test_id' => $testId]);
                continue;
            }

            $latestResult = collect($userResults)->sortByDesc('created_at')->first();

            $interpretation = $this->interpretTest($test, $latestResult);

            $testData = [
                'id' => $test->id,
                'code' => $test->code,
                'name' => $test->name ?? 'Тест #' . $testId,
                'date' => $latestResult->created_at->format('d.m.Y H:i'),
                'interpretation' => $interpretation,
            ];

            // Только для color_test добавляем специальные поля
            if ($test->code === 'color_test') {
                $testData['color_result'] = [
                    'ml_predictions' => $latestResult->ml_predictions,
                    'user_svg' => $latestResult->user_image,
                    'user_image' => $this->convertSvgToBase64Png($latestResult->user_image),

                ];
            }

            $analysis['tests'][] = $testData;
        }

        return $analysis;
    }

    /**
     * Интерпретация конкретного теста
     */
    private function interpretTest($test, $result): array
    {
        $data = json_decode($result->data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $data = ['error' => 'Ошибка декодирования данных'];
        }

        switch ($test->code) {
            case 'holland':
                return $this->interpretHolland($data);
            case 'interest_map':
                return $this->interpretInterestsMap($data);

            case 'ddo':
                return $this->interpretDDO($data);

            case 'jovayshi':
                return $this->interpretJovayshi($data);

            case 'profession_matrix':
                return $this->interpretProfessionMatrix($data);

            case 'eisenck_state':
                return $this->interpretEisenckState($data);

            case 'jung_character':
                return $this->interpretJungCharacter($data);

            case 'thomas_conflict':
                return $this->interpretThomasConflict($data);

            case 'keirsey':
                return $this->interpretKeirsey($data);

            case 'leader_organizer':
                return $this->interpretLeaderOrganizer($data);

            case 'self_development':
                return $this->interpretSelfDevelopment($data);

            default:
                return [
                    'raw_data' => $data,
                    'message' => 'Интерпретация для данного теста не настроена'
                ];
        }
    }

    /**
     * ИНТЕРПРЕТАЦИЯ ТЕСТА ХОЛЛАНДА
     */
    private function interpretHolland($data): array
    {
        // Если данные пришли как объект, конвертируем в массив
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Маппинг кодов на русские названия
        $typeNames = [
            'R' => 'Реалистический',
            'I' => 'Интеллектуальный',
            'S' => 'Социальный',
            'C' => 'Конвенциальный',
            'E' => 'Предприимчивый',
            'A' => 'Артистичный'
        ];

        // Сортируем по убыванию процентов
        arsort($data);

        $result = [
            'types' => []
        ];

        foreach ($data as $code => $percentage) {
            $result['types'][$code] = [
                'code' => $code,
                'name' => $typeNames[$code] ?? $code,
                'percentage' => round($percentage, 1)
            ];
        }

        // Определяем доминирующий тип (с максимальным процентом)
        $result['dominant'] = $result['types'][array_key_first($data)] ?? null;

        return $result;
    }

    /**
     * ИНТЕРПРЕТАЦИЯ КАРТЫ ИНТЕРЕСОВ
     */
    private function interpretInterestsMap($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Сортируем по убыванию баллов
        arsort($data);

        $result = [
            'interests' => []
        ];

        foreach ($data as $key => $score) {
            $interest = [
                'name' => $key,  // Используем оригинальное название из ключа
                'score' => (int)$score
            ];

            $result['interests'][$key] = $interest;
        }

        return $result;
    }
    /**
     * ИНТЕРПРЕТАЦИЯ ДДО КЛИМОВА
     */
    private function interpretDDO($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Преобразуем в проценты (максимум 8 баллов)
        $percentages = [];
        foreach ($data as $type => $score) {
            $percentages[$type] = round(($score / 8) * 100, 1);
        }

        // Сортируем по убыванию процентов
        arsort($percentages);

        $result = [
            'types' => []
        ];

        foreach ($percentages as $type => $percentage) {
            $result['types'][$type] = [
                'name' => $type,
                'score' => $data[$type],
                'percentage' => $percentage
            ];
        }

        return $result;
    }

    /**
     * ИНТЕРПРЕТАЦИЯ ОПРОСНИКА ЙОВАЙШИ
     */
    private function interpretJovayshi($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        $typeNames = [
            'I' => 'Склонность к работе с людьми',
            'II' => 'Склонность к исследовательской (интеллектуальной) работе',
            'III' => 'Склонность к практической деятельности',
            'IV' => 'Склонность к эстетическим видам деятельности',
            'V' => 'Склонность к экстремальным видам деятельности',
            'VI' => 'Склонность к планово-экономическим видам деятельности'
        ];

        // Сортируем по убыванию баллов
        arsort($data);

        $result = [
            'types' => []
        ];

        foreach ($data as $code => $score) {
            $percentage = round(($score / 12) * 100, 1); // Максимум 12 баллов

            $result['types'][$code] = [
                'code' => $code,
                'name' => $typeNames[$code] ?? $code,
                'score' => $score,
                'percentage' => $percentage
            ];
        }

        return $result;
    }
    /**
     * ИНТЕРПРЕТАЦИЯ МАТРИЦЫ ПРОФЕССИЙ
     */
    private function interpretProfessionMatrix($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        $objects = isset($data['object']) ? $data['object'] : [];
        $activities = isset($data['activity']) ? $data['activity'] : [];

        $result = [
            'objects' => $objects,
            'activities' => $activities,
            'professions' => []
        ];

        // Матрица профессий из предоставленной таблицы
        // Ключ: activity-object (строка-столбец)
        $professionMatrix = [
            // Управление (activity 1)
            '1-1' => ['Менеджер по персоналу', 'Администратор'],
            '1-2' => ['Маркетолог', 'Диспетчер', 'Статистик'],
            '1-3' => ['Экономист', 'Аудитор', 'Аналитик'],
            '1-4' => ['Технолог', 'Авиадиспетчер', 'Инженер'],
            '1-5' => ['Режиссер', 'Продюсер', 'Дирижер'],
            '1-6' => ['Кинолог', 'Зоотехник', 'Генный инженер'],
            '1-7' => ['Агроном', 'Фермер', 'Селекционер'],
            '1-8' => ['Товаровед', 'Менеджер по продажам'],
            '1-9' => ['Менеджер по продажам', 'Логистик', 'Товаровед'],
            '1-10' => ['Энергетик', 'Инженер по кадастру'],

            // Обслуживание (activity 2)
            '2-1' => ['Продавец', 'Парикмахер', 'Официант'],
            '2-2' => ['Переводчик', 'Экскурсовод', 'Библиотекарь'],
            '2-3' => ['Бухгалтер', 'Кассир', 'Инкассатор'],
            '2-4' => ['Водитель', 'Слесарь', 'Теле-радиомастер'],
            '2-5' => ['Гример', 'Костюмер', 'Парикмахер'],
            '2-6' => ['Животновод', 'Птицевод', 'Скотовод'],
            '2-7' => ['Овощевод', 'Полевод', 'Садовод'],
            '2-8' => ['Экспедитор', 'Упаковщик', 'Продавец'],
            '2-9' => ['Продавец', 'Упаковщик', 'Экспедитор'],
            '2-10' => ['Егерь', 'Лесник', 'Мелиоратор'],

            // Образование (activity 3)
            '3-1' => ['Учитель', 'Воспитатель', 'Социальный педагог'],
            '3-2' => ['Преподаватель', 'Ведущий теле- и радиопрограмм'],
            '3-3' => ['Консультант', 'Преподаватель экономики'],
            '3-4' => ['Мастер производственного обучения'],
            '3-5' => ['Хореограф', 'Преподаватель музыки', 'Преподаватель живописи'],
            '3-6' => ['Дрессировщик', 'Кинолог', 'Жокей'],
            '3-7' => ['Преподаватель биологии', 'Эколог'],
            '3-8' => ['Мастер производственного обучения'],
            '3-9' => ['Мастер производственного обучения'],
            '3-10' => ['Преподаватель', 'Эколог'],

            // Оздоровление (activity 4)
            '4-1' => ['Врач', 'Медсестра', 'Тренер'],
            '4-2' => ['Рентгенолог', 'Врач (компьютерная диагностика)'],
            '4-3' => ['Антикризисный управляющий', 'Страховой агент'],
            '4-4' => ['Мастер автосервиса', 'Физиотерапевт'],
            '4-5' => ['Пластический хирург', 'Косметолог', 'Реставратор'],
            '4-6' => ['Ветеринар', 'Лаборант питомника', 'Зоопсихолог'],
            '4-7' => ['Фитотерапевт', 'Гомеопат', 'Травник'],
            '4-8' => ['Диетолог', 'Косметолог', 'Санитарный инспектор'],
            '4-9' => ['Фармацевт', 'Ортопед', 'Протезист'],
            '4-10' => ['Бальнеолог', 'Эпидемиолог', 'Лаборант'],

            // Творчество (activity 5)
            '5-1' => ['Режиссер', 'Артист', 'Музыкант'],
            '5-2' => ['Программист', 'Редактор', 'Web-дизайнер'],
            '5-3' => ['Менеджер по проектам', 'Продюсер'],
            '5-4' => ['Конструктор', 'Дизайнер', 'Художник'],
            '5-5' => ['Художник', 'Писатель', 'Композитор'],
            '5-6' => ['Дрессировщик', 'Служитель цирка', 'Флорист'],
            '5-7' => ['Фитодизайнер', 'Озеленитель', 'Флорист'],
            '5-8' => ['Кондитер', 'Повар', 'Кулинар'],
            '5-9' => ['Резчик по дереву', 'Витражист', 'Скульптор'],
            '5-10' => ['Архитектор', 'Мастер-цветов', 'Декоратор'],

            // Производство (activity 6)
            '6-1' => ['Мастер производственного обучения'],
            '6-2' => ['Корректор', 'Журналист', 'Полиграфист'],
            '6-3' => ['Экономист', 'Бухгалтер', 'Кассир'],
            '6-4' => ['Станочник', 'Аппаратчик', 'Машинист'],
            '6-5' => ['Ювелир', 'График', 'Керамист'],
            '6-6' => ['Животновод', 'Птицевод', 'Рыбовод'],
            '6-7' => ['Овощевод', 'Цветовод', 'Садовод'],
            '6-8' => ['Технолог', 'Калькулятор', 'Повар'],
            '6-9' => ['Швея', 'Кузнец', 'Столяр'],
            '6-10' => ['Шахтер', 'Нефтяник', 'Техник'],

            // Конструирование (activity 7)
            '7-1' => ['Стилист', 'Пластический хирург'],
            '7-2' => ['Картограф', 'Программист', 'Web-мастер'],
            '7-3' => ['Плановик', 'Менеджер по проектам'],
            '7-4' => ['Инженер-конструктор', 'Телемастер'],
            '7-5' => ['Архитектор', 'Дизайнер', 'Режиссер'],
            '7-6' => ['Генный инженер', 'Виварист', 'Селекционер'],
            '7-7' => ['Селекционер', 'Ландшафтист', 'Флорист'],
            '7-8' => ['Инженер-технолог', 'Кулинар'],
            '7-9' => ['Модельер', 'Закройщик', 'Обувщик'],
            '7-10' => ['Дизайнер ландшафта', 'Инженер'],

            // Исследование (activity 8)
            '8-1' => ['Психолог', 'Следователь', 'Лаборант'],
            '8-2' => ['Социолог', 'Математик', 'Аналитик'],
            '8-3' => ['Аудитор', 'Экономист', 'Аналитик'],
            '8-4' => ['Испытатель (техники)', 'Хронометражист'],
            '8-5' => ['Искусствовед', 'Критик', 'Журналист'],
            '8-6' => ['Зоопсихолог', 'Орнитолог', 'Ихтиолог'],
            '8-7' => ['Биолог', 'Ботаник', 'Микробиолог'],
            '8-8' => ['Лаборант', 'Дегустатор', 'Санитарный врач'],
            '8-9' => ['Эргономик', 'Контролер', 'Лаборант'],
            '8-10' => ['Биолог', 'Метеоролог', 'Агроном'],

            // Защита (activity 9)
            '9-1' => ['Милиционер', 'Военный', 'Адвокат'],
            '9-2' => ['Арбитр', 'Юрист', 'Патентовед'],
            '9-3' => ['Инкассатор', 'Охранник', 'Страховой агент'],
            '9-4' => ['Пожарный', 'Сапер', 'Инженер'],
            '9-5' => ['Постановщик трюков', 'Каскадер'],
            '9-6' => ['Егерь', 'Лесничий', 'Инспектор рыбнадзора'],
            '9-7' => ['Эколог', 'Микробиолог', 'Миколог'],
            '9-8' => ['Санитарный врач'],
            '9-9' => ['Сторож', 'Инспектор'],
            '9-10' => ['Охрана ресурсов', 'Инженер по ТБ'],

            // Контроль (activity 10)
            '10-1' => ['Таможенник', 'Прокурор', 'Табельщик'],
            '10-2' => ['Корректор', 'Системный программист'],
            '10-3' => ['Ревизор', 'Налоговый полицейский'],
            '10-4' => ['Техник-контролер', 'Обходчик ЖД'],
            '10-5' => ['Выпускающий редактор', 'Консультант'],
            '10-6' => ['Консультант', 'Эксперт по экстерьеру'],
            '10-7' => ['Селекционер', 'Агроном', 'Лаборант'],
            '10-8' => ['Дегустатор', 'Лаборант', 'Санитарный врач'],
            '10-9' => ['Оценщик', 'Контролер ОТК', 'Приемщик'],
            '10-10' => ['Радиолог', 'Почвовед', 'Эксперт'],
        ];

        // Собираем профессии по всем комбинациям
        $professionList = [];
        foreach ($activities as $activity) {
            foreach ($objects as $object) {
                $key = $activity . '-' . $object;
                if (isset($professionMatrix[$key])) {
                    foreach ($professionMatrix[$key] as $prof) {
                        $professionList[] = $prof;
                    }
                }
            }
        }

        // Убираем дубликаты и сортируем
        $professionList = array_unique($professionList);
        sort($professionList);

        $result['professions'] = $professionList;

        return $result;
    }

    /**
     * ИНТЕРПРЕТАЦИЯ ТЕСТА АЙЗЕНКА (САМООЦЕНКА ПСИХИЧЕСКИХ СОСТОЯНИЙ)
     */
    private function interpretEisenckState($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        $result = [];

        // Проверяем, есть ли уже готовая интерпретация
        if (isset($data['interpretation']) && is_array($data['interpretation'])) {
            foreach ($data['interpretation'] as $scale => $scaleData) {
                $result[$scale] = [
                    'score' => $scaleData['score'],
                    'level' => $scaleData['level'],
                    'percent' => $scaleData['percent'] ?? round(($scaleData['score'] / 20) * 100, 2)
                ];
            }
        }


        return $result;
    }



    /**
     * ИНТЕРПРЕТАЦИЯ МЕТОДИКИ ОПРЕДЕЛЕНИЯ ТИПА ХАРАКТЕРА ПО К. ЮНГУ
     */
    private function interpretJungCharacter($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Извлекаем данные напрямую из массива
        return [
            'score' => $data['score'] ?? 0,
            'type' => $data['type'] ?? 'не определен'
        ];
    }

    /**
     * ИНТЕРПРЕТАЦИЯ ТЕСТА ТОМАСА (ТИПЫ ПОВЕДЕНИЯ В КОНФЛИКТЕ)
     */
    private function interpretThomasConflict($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Извлекаем scores из данных
        $scores = isset($data['scores']) ? $data['scores'] : [];


        // Сортируем по убыванию баллов
        arsort($scores);

        $result = [
            'strategies' => []
        ];

        foreach ($scores as $strategy => $score) {
            $percentage = round(($score / 12) * 100, 1);
            $result['strategies'][$strategy] = [
                'name' => $strategy,
                'score' => $score,
                'percentage' => $percentage
            ];
        }



        return $result;
    }

    /**
     * ИНТЕРПРЕТАЦИЯ ОПРОСНИКА Д.КЕЙРСИ
     */
    private function interpretKeirsey($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        return [
            'personality_type' => $data['personality_type'] ?? '',
            'socionic_name' => $data['socionic_name'] ?? '',
            'scores' => $data['scores'] ?? []
        ];
    }
    /**
     * ИНТЕРПРЕТАЦИЯ ТЕСТА «ОРГАНИЗАТОРСКИЕ СПОСОБНОСТИ ЛИДЕРА»
     */
    private function interpretLeaderOrganizer($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        return [
            'score' => $data['score'] ?? 0,
            'percentage' => $data['percentage'] ?? 0,
            'level' => $data['level'] ?? ''
        ];
    }

    /**
     * ИНТЕРПРЕТАЦИЯ ТЕСТА НА СПОСОБНОСТЬ К САМОРАЗВИТИЮ И САМООБРАЗОВАНИЮ
     */
    private function interpretSelfDevelopment($data): array
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        return [
            'score' => $data['score'] ?? 0,
            'percentage' => $data['percentage'] ?? 0,
            'level' => $data['level'] ?? 0,
            'level_name' => $data['level_name'] ?? ''
        ];
    }
    private function convertSvgToBase64Png($svgCode)
    {


        if (empty($svgCode)) {
            return null;
        }
        $svgWithAttrs = preg_replace_callback(
            '/<path([^>]*)>/i',
            function ($matches) {
                $attrs = $matches[1];

                // Проверяем наличие stroke (как атрибут или в style)
                $hasStroke = (strpos($attrs, 'stroke=') !== false) ||
                    (strpos($attrs, 'stroke:') !== false);

                if (!$hasStroke) {
                    // Проверяем, есть ли style атрибут
                    if (preg_match('/style="([^"]*)"/', $attrs, $styleMatch)) {
                        // Добавляем stroke в существующий style
                        $newStyle = $styleMatch[1] . '; stroke:black; stroke-width:0.5';
                        $attrs = str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attrs);
                    } else {
                        // Создаем новый style атрибут
                        $attrs .= ' style="stroke:black; stroke-width:0.5"';
                    }
                }

                return '<path' . $attrs . '>';
            },
            $svgCode
        );

        $processedSvg = preg_replace(
            '/stroke="[^"]*"/i',
            'stroke="black"',
            $svgCode
        );
        $wrappedSvg = '<?xml version="1.0" encoding="UTF-8"?>
    <svg xmlns="http://www.w3.org/2000/svg"
         width="378"
         height="481"
         viewBox="0 0 378 481">
        <rect width="100%" height="100%" fill="white"/>
        ' . $processedSvg . '
    </svg>';

        $tempDir = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                return null;
            }
        }

        if (!is_writable($tempDir)) {
            return null;
        }

        // Создаем файлы с уникальными именами
        $svgFile = $tempDir . '/svg_' . uniqid() . '.svg';
        $pngFile = $tempDir . '/png_' . uniqid() . '.png';


        // Сохраняем SVG
        $writeResult = file_put_contents($svgFile, $wrappedSvg);


        if ($writeResult === false) {
            return null;
        }

        if (!file_exists($svgFile)) {
            return null;
        }

        $magickPath = '/opt/homebrew/bin/magick';

        $command = "/opt/homebrew/bin/magick -define registry:temporary-path={$tempDir} \"{$svgFile}\" \"{$pngFile}\" 2>&1";

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($pngFile)) {

            if (file_exists($svgFile)) unlink($svgFile);
            return null;
        }

        // Читаем PNG и конвертируем в base64
        $pngData = file_get_contents($pngFile);

        $base64Png = 'data:image/png;base64,' . base64_encode($pngData);

        unlink($svgFile);
        unlink($pngFile);

        return $base64Png;
    }
}
