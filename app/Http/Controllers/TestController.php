<?php

namespace App\Http\Controllers;




use App\Models\Chess;
use App\Models\Isa;
use App\Models\RectanglesForIsa;
use App\Models\RectanglesForResult;
use App\Models\Results;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;

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
                'match'=>null,
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

            return response()->json(['message' => 'Результат успешно сохранен']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка: ' . $e->getMessage()], 500);
        }
    }
    private function analyzeResult($result, $svg)
    {
        
        $userRectangles = RectanglesForResult::where('result_id', $result->id)->get();

        $bestMatch = null;
        $highestMatch = 0;
        $match='';
        
        $isas = Isa::all();
        foreach ($isas as $isa) {
            $templateRectangles = RectanglesForIsa::where('isa_id', $isa->id)->get();

            // $totalRectangles = count($templateRectangles);
            // if ($totalRectangles === 0) {
            //     continue;
            // }

            $matches = $this->compareRectangles($templateRectangles, $userRectangles);

            $matchPercentage = $matches['count'];
            if ($isa->individual_style_of_activity == 'авангардист'){
                $matchPercentage*=0.8;
            }
            $match .= $isa->individual_style_of_activity . "-" . $matchPercentage . "     ";
            // $match .= $matchPercentage . "%     ";
      
            if ($matchPercentage > $highestMatch) {
                $highestMatch = $matchPercentage;
                $bestMatch = $isa;
            }
        }

        
        $contrastScore = $this->calculateContrastScore($userRectangles);

        
        $chessStructureLevel = $this->getChessStructureLevel($contrastScore);


        
        $result->isa_id = $bestMatch->id;
        $result->industry = $bestMatch->individual_style_of_activity; 
        $result->recommendation =  $this->generateRecommendation($bestMatch->individual_style_of_activity, $svg, $chessStructureLevel->chess_structure);
        $result->chess_structure = $chessStructureLevel->chess_structure;
        $result->chess_structure_id = $chessStructureLevel->id;
        $result->match = $match;
        $result->save();
    }

    private function compareRectangles($templateRectangles, $userRectangles)
    {
        $matchCount = 0;
        foreach ($userRectangles as $uRect) {
            foreach ($templateRectangles as $tRect) {
                if (
                    $this->areColorsSimilar($tRect->color == "white" ? "rgb(255, 255, 255)" : $tRect->color, $uRect->color == "white" ? "rgb(255, 255, 255)" : $uRect->color) &&
                    abs($tRect->x - $uRect->x) <= 1 &&
                    abs($tRect->y - $uRect->y) <= 1 &&
                    abs($tRect->z - $uRect->z) <= 3
                ) {
                    $matchCount++;
                }
            }
        }
        return ['count' => $matchCount];
    }

    private function areColorsSimilar($color1, $color2, $tolerance = 125)
    {
        
        $rgb1 = $this->extractRgb($color1);
        $rgb2 = $this->extractRgb($color2);

        
        return abs($rgb1['r'] - $rgb2['r']) <= $tolerance &&
            abs($rgb1['g'] - $rgb2['g']) <= $tolerance &&
            abs($rgb1['b'] - $rgb2['b']) <= $tolerance;
    }

    private function extractRgb($color)
    {
        
        $rgb = [];
        preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches);
        $rgb['r'] = (int)$matches[1];
        $rgb['g'] = (int)$matches[2];
        $rgb['b'] = (int)$matches[3];
        return $rgb;
    }
    private function calculateContrastScore($rectangles)
    {
        $totalContrast = 0;
        $comparisonCount = 0;

        
        $rectangles = $rectangles->sortBy('x')->sortBy('y')->sortBy('z');

        foreach ($rectangles as $i => $rect1) {
            foreach ($rectangles as $j => $rect2) {
                
                if (
                    ($rect1->x == $rect2->x && (
                        (abs($rect1->y - $rect2->y) == 1 && $rect1->z == $rect2->z) || 
                        (abs($rect1->z - $rect2->z) == 1 && $rect1->y == $rect2->y)    
                    )) ||
                    (
                        (($rect1->y == 1 && $rect2->y == 3) || ($rect1->y == 3 && $rect2->y == 1)) &&
                        $rect1->z == $rect2->z &&
                        abs($rect1->x - $rect2->x) == 1)  

                ) {
                    $rgb1 = $this->extractRgb($rect1->color);
                    $rgb2 = $this->extractRgb($rect2->color);

                    
                    $luminance1 = $this->calculateLuminance($rgb1);
                    $luminance2 = $this->calculateLuminance($rgb2);

                    $contrastRatio = ($luminance1 > $luminance2)
                        ? (($luminance1 + 0.05) / ($luminance2 + 0.05))
                        : (($luminance2 + 0.05) / ($luminance1 + 0.05));

                    $totalContrast += $contrastRatio;
                    $comparisonCount++;
                }
            }
        }

        
        return $comparisonCount > 0 ? $totalContrast / $comparisonCount : 0;
    }

    private function calculateLuminance($rgb)
    {
        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $r = ($r <= 0.03928) ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = ($g <= 0.03928) ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = ($b <= 0.03928) ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    private function getChessStructureLevel($contrastScore)
    {
        
        if ($contrastScore >= 4.5) { 
            return Chess::find(1);
        } elseif ($contrastScore >= 3) { 
            return Chess::find(2);
        } else {
            return Chess::find(3);
        }
    }
    private function generateRecommendation($style, $svg, $chessStructureLevel)
    {

        $result = [
            'творец' => [
                'сильная' => '<ol><li>Программная инженерия</li><li>Актерское искусство</li><li>Дизайн</li></ol>',
                'средняя' => '<ol><li>Программная инженерия</li><li>Архитектура</li><li>Искусство народного пения</li></ol>',
                'слабая' => '<ol><li>Веб-дизайн</li><li>Программная инженерия</li><li>Архитектура и строительство</li></ol>'
            ],
            'авангардист' => [
                'сильная' => '<ol><li>Математика и компьютерные науки</li><li>Фундаментальная информатика и информационные технологии</li><li>Фотоника и оптоинформатика</li></ol>',
                'средняя' => '<ol><li>Информационные технологии</li><li>Информатика и вычислительная техника</li><li>Медиакоммуникации</li></ol>',
                'слабая' => '<ol><li>Бизнес-информатика</li><li>Фундаментальная информатика и информационные технологии</li><li>Математика и компьютерные науки</li><li>Теология</li></ol>'
            ],
            'смешанный' => [
                'сильная' => '<ol><li>Программная инженерия</li><li>Информатика и вычислительная техника</li><li>Прикладная математика</li></ol>',
                'средняя' => '<ol><li>Прикладная математика и информатика</li><li>Математика и компьютерные науки</li><li>Фундаментальная информатика и информационные технологии</li></ol>',
                'слабая' => '<ol><li>Математика и компьютерные науки</li><li>Программная инженерия</li><li>Информатика и вычислительная техника</li></ol>'
            ],
            'рационал' => [
                'сильная' => '<ol><li>Фундаментальная информатика и информационные технологии</li><li>Физика/Химия</li><li>Фотоника и оптоинформатика</li></ol>',
                'средняя' => '<ol><li>Программная инженерия</li><li>Фундаментальная и прикладная химия</li><li>Радиоэлектронные системы и комплексы</li></ol>',
                'слабая' => '<ol><li>Математика и компьютерные науки</li><li>Электроника/электротехника</li><li>Безопасность</li></ol>'
            ],
            'скептик' => [
                'сильная' => '<ol><li>Психология</li><li>Социология</li><li>Социальная работа</li></ol>',
                'средняя' => '<ol><li>Политология</li><li>Государственное и муниципальное управление</li><li>Бизнес-информатика</li></ol>',
                'слабая' => '<ol><li>Юриспруденция</li><li>Правоохранительная деятельность</li><li>Экономическая безопасность</li></ol>'
            ]
        ];
        
       
        $response = $result[$style][$chessStructureLevel];
        // $prompt = "Я разукрашивал картинку, на которой изображен тонель с прямоугольниками по стенам. Мой индивидуальный стиль активности, судя по результату раскрашивания - '{$style}' и у моего результата степень контрастности {$chessStructureLevel}. Какие профессии ты можешь мне предложить из следующего списка? Продукты питания животного происхождения, Агрономия, Технология производства и переработки сельскохозяйственной продукции, Ландшафтная архитектура, Зоотехния, Ветеринария, Агрономия, Архитектура, Дизайн архитектурной среды, Строительство, Строительство уникальных зданий и сооружений, География, Картография и геоинформатика, Экология и природопользование, Землеустройство и кадастры, Туризм, Теплоэнергетика и теплотехника, Электроэнергетика и электротехника, Техносферная безопасность, Эксплуатация транспортно-технологических машин и комплексов, Агроинженерия, Пожарная безопасность, Реклама и связи с общественностью, Медиакоммуникации, Педагогическое образование, Народная художественная культура, Библиотечно-информационная деятельность, Искусство народного пения, Дизайн, Актерское искусство, Культурология, Физика, Химия, физика и механика материалов, Фотоника и оптоинформатика, Химическая технология, Фундаментальная и прикладная химия, Радиоэлектронные системы и комплексы, Информатика и вычислительная техника, Информационная безопасность, Инфокоммуникационные технологии и системы связи, Электроника и наноэлектроника, Оптотехника, Электроэнергетика и электротехника, Стандартизация и метрология, Сервис, Информатика и вычислительная техника, Психология, Социология,Социальная работа, Политология, Педагогическое образование, История, Теология, Лечебное дело, Педиатрия, Стоматология, Фармация,General medicineАвтоматизация технологических процессов и производств, Конструкторско-технологическое обеспечение машиностроительных производств,Конструкторско-технологическое обеспечение машиностроительных производств, Биология, Биотехнология, Биоинженерия и биоинформатика, Лингвистика, Перевод и переводоведение, Филология, Прикладная математика и информатика, Математика и компьютерные науки, Фундаментальная информатика и информационные технологии, Программная инженерия, Прикладная математика и информатика, Журналистика, Статистика, Управление качеством, Экономика, Менеджмент, Экономическая безопасность, Государственное и муниципальное управление, Бизнес-информатика, Юриспруденция, Правоохранительная деятельность.  Предложи не больше 5 схожих направлений";
        // $response = $this->callGigaChat($prompt);

        return $response;
    }

    // private function getNewApiKey()
    // {
        
    //     $url = 'https://ngw.devices.sberbank.ru:9443/api/v2/oauth';

        
    //     $response = Http::withOptions([
    //         'verify' => false, 
    //     ])->withHeaders([
    //         'Content-Type' => 'application/x-www-form-urlencoded',
    //         'Accept' => 'application/json',
    //         'RqUID' => '6ed62f30-4705-48fb-9c27-179731edf665',
    //         'Authorization' => 'Basic Njg4NzUzZmEtNGM1Zi00YzY0LWJiYjktMjQxNjVmMGQ4OTZhOjY4YjZkOTc3LTdjNjYtNGFjNi1iODQ5LTgxN2IyOGI0MWNkZA==',  
    //     ])->asForm()->post($url, [
    //         'scope' => 'GIGACHAT_API_PERS',
    //     ]);

        
    //     if ($response->failed()) {
    //         throw new Exception('Ошибка получения токена: ' . $response->body());
    //     }

        
    //     $data = $response->json();
    //     $token = $data['access_token'];
    //     $expiresAt = $data['expires_at']; 

        
    //     env('GIGACHAT_API_KEY', $token);
    //     env('GIGACHAT_TOKEN_EXPIRATION', $expiresAt);

    //     return $token;
    // }

    // private function getTokenExpiration()
    // {
        
    //     return env('GIGACHAT_TOKEN_EXPIRATION');
    // }
    // private function callGigaChat($prompt)
    // {
    //     $apiKey = env('GIGACHAT_API_KEY'); 
    //     $tokenExpiration = env('GIGACHAT_TOKEN_EXPIRATION'); 

    //     $currentTimestamp = round(microtime(true) * 1000); 

        
    //     if ($currentTimestamp >= $tokenExpiration) {
            
    //         $apiKey = $this->getNewApiKey(); 
    //         $tokenExpiration = $this->getTokenExpiration(); 
    //     }

    //     $response = Http::withOptions([
    //         'verify' => false, 
    //     ])->withHeaders([
    //         'Authorization' => 'Bearer ' . $apiKey,
    //     ])->post('https://gigachat.devices.sberbank.ru/api/v1/chat/completions', [
    //         'model' => 'GigaChat', 
    //         'messages' => [[
    //             'role' => 'user',
    //             'content' => $prompt 
    //         ]],
    //         'stream' => false,
    //         'repetition_penalty' => 1,
    //     ]);

    //     if ($response->failed()) {
    //         return response()->json(['error' => 'Ошибка GigaChat API: ' . $response->body()], 500); 
    //     }

    //     $responseJson = $response->json();
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return response()->json(['error' => 'Ошибка парсинга JSON от GigaChat: ' . json_last_error_msg()], 500);
    //     }
    //     $recommendations = $responseJson['choices'][0]['message']['content'] ?? 'Не удалось получить ответ.';

        
    //     $formattedRecommendations = '<ol>';
    //     foreach (explode("\n", $recommendations) as $line) {
    //         if (preg_match('/^(\d+)\.\s*\*\*(.+?)\*\*\s*(.+)$/', $line, $matches)) {
    //             $formattedRecommendations .= '<li>' . trim($matches[2]) . '</li>';
    //         }
    //     }

    //     $formattedRecommendations .= '</ol>';

        
    //     return $formattedRecommendations;
    // }
    public function showResults()
    {
        $userId = Auth::id();

        $results = Results::where('user_id', $userId)->get();
        if (!$results) {
            return redirect()->route('profile.index')->with('error', 'Результаты не найдены');
        }
        return view('profile.results', compact('results'));
    }
    public function showResult($id)
    {
        $userId = Auth::id();

        $result = Results::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$result) {
            return redirect()->route('profile.results')->with('error', 'Результат не найден');
        }

        return view('profile.result', compact('result'));
    }
    public function search(Request $request)
    {
        $field = $request->input('field');
        $query = $request->input('query');

        
        $validFields = ['isa', 'chess_structure', 'created_at', 'recommendation'];

        if (!in_array($field, $validFields)) {
            return redirect()->route('results')->with('error', 'Неверное поле для поиска.');
        }

        
        $currentUserId = auth()->id();

        
        if ($field === 'chess_structure') {
            $results = Results::join('chesses', 'results.chess_structure_id', '=', 'chesses.id')
                ->where('results.user_id', $currentUserId) 
                ->where('chesses.chess_structure', 'LIKE', "%{$query}%")
                ->select('results.*') 
                ->get();
        } elseif ($field === 'isa') {
            $results = Results::join('isas', 'results.isa_id', '=', 'isas.id')
                ->where('results.user_id', $currentUserId) 
                ->where('isas.individual_style_of_activity', 'LIKE', "%{$query}%")
                ->select('results.*') 
                ->get();
        } else {
            $results = Results::where('user_id', $currentUserId) 
                ->where($field, 'LIKE', "%{$query}%")
                ->get();
        }

        return view('profile.results', compact('results'));
    }
}
