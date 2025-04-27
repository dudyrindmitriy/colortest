<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\RectanglesForResult;
use App\Models\Results;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function isaDistribution()
    {
        return Results::with('isa')
            ->selectRaw('isa_id, count(*) as count')
            ->groupBy('isa_id')
            ->get()
            ->map(function ($item) {
                return [
                    'style' => $item->isa->individual_style_of_activity ?? 'Не указан',
                    'count' => $item->count
                ];
            })->toArray();
    }
    public function chessDistribution()
    {
        return Results::with('chess')
            ->selectRaw('chess_structure_id, count(*) as count')
            ->groupBy('chess_structure_id')
            ->get()
            ->map(function ($item) {
                return [
                    'chess_structure' => $item->chess->chess_structure ?? 'Не указан',
                    'count' => $item->count
                ];
            })->toArray();
    }
    public function testPassagesOverTime()
    {
        return Results::selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('Y-m-d'),
                    'count' => $item->count,
                ];
            })->toArray();
    }
    public function areaDistribution()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();


        $actualData = Results::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('count', 'month')
            ->toArray();


        $seasonalCoefficients = [
            1 => 0.8,
            2 => 0.9,
            3 => 1.0,
            4 => 1.1,
            5 => 1.3,
            6 => 0.7,
            7 => 0.6,
            8 => 0.8,
            9 => 1.5,
            10 => 1.3,
            11 => 1.1,
            12 => 0.9
        ];

        $average = 10;

        $result = [];
        $current = Carbon::now()->startOfMonth()->subMonths(11);

        for ($i = 0; $i < 12; $i++) {
            $date = $current->copy()->addMonths($i);
            $month = $date->month;
            $monthName = $date->translatedFormat('M');


            $expected = $average * $seasonalCoefficients[$month];


            if (isset($actualData[$month])) {
            }


            $result[] = [
                'month' => $monthName,
                'expected' => round(max($expected, 5)),
                'actual' => $actualData[$month] ?? 0
            ];
        }

        return $result;
    }


    public function bubbleDistribution()
    {

        $colorStats = RectanglesForResult::select(
            'color',
            DB::raw('COUNT(*) as frequency'),

            DB::raw('AVG(y) as prof_association')
        )
            ->groupBy('color')
            ->get();



        return $colorStats->map(function ($item) {
            preg_match_all('/\d+/', $item->color, $matches);
            $r = $matches[0][0] ?? 0;
            $g = $matches[0][1] ?? 0;
            $b = $matches[0][2] ?? 0;

            $brightness = sqrt(
                0.299 * pow($r, 2) +
                    0.587 * pow($g, 2) +
                    0.114 * pow($b, 2)
            ) / 255;
            $r_norm = $r / 255;
            $g_norm = $g / 255;
            $b_norm = $b / 255;
            $r_norm = $r_norm <= 0.03928 ? $r_norm / 12.92 : pow(($r_norm + 0.055) / 1.055, 2.4);
            $g_norm = $g_norm <= 0.03928 ? $g_norm / 12.92 : pow(($g_norm + 0.055) / 1.055, 2.4);
            $b_norm = $b_norm <= 0.03928 ? $b_norm / 12.92 : pow(($b_norm + 0.055) / 1.055, 2.4);
            $contrast = 0.2126 * $r_norm + 0.7152 * $g_norm + 0.0722 * $b_norm;
            return [
                'color_name' => $item->color,
                'color_code' => sprintf('#%02X%02X%02X', $r, $g, $b),
                'brightness' => $brightness,
                'frequency' => (int)$item->frequency,
                'contrast' => $contrast,
                'color_rgb' => [$r, $g, $b]
            ];
        })->toArray();
    }


    public function calendarDistribution()
    {
        $startDate = Carbon::now()->subMonths(3);

        return Results::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('DAY(created_at) as day'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date', 'day', 'month', 'year')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'day' => (int)$item->day,
                    'month' => (int)$item->month,
                    'year' => (int)$item->year,
                    'count' => (int)$item->count
                ];
            })
            ->toArray();
    }


    public function orgDistribution()
    {
        $isas = Isa::all();
        $chessStructures = Chess::all();

        $data = [];
        $data[] = ['id' => 'Цвета', 'parent' => '', 'tooltip' => 'Корневой элемент'];

        foreach ($isas as $isa) {
            $isaId = 'Стиль: ' . $isa->individual_style_of_activity;


            $data[] = [
                'id' => $isaId,
                'parent' => 'Цвета',
                'tooltip' => $isa->individual_style_of_activity
            ];


            foreach ($chessStructures as $chess) {
                $chessId = 'Шахматная структура: ' . $chess->chess_structure . '_' . $isa->id;

                $data[] = [
                    'id' => $chessId,
                    'parent' => $isaId,
                    'tooltip' => $chess->chess_structure
                ];
            }
        }

        return $data;
    }



   
    public function sankeyDistribution()
    {

        $associations = DB::table('results as r')
            ->join('users as u', 'r.user_id', '=', 'u.id')
            ->select(
                'r.user_id',
                'u.login as user_login',
                'r.recommendation',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('r.user_id', 'u.login', 'r.recommendation')
            ->get();


        $nodes = [];
        $links = [];
        $nodeIds = [];

        foreach ($associations as $item) {
            $userNode = $item->user_login;
            $profNode = strip_tags($item->recommendation);


            if (!in_array($userNode, $nodeIds)) {
                $nodeIds[] = $userNode;
                $nodes[] = ['id' => $userNode, 'name' => $item->user_login];
            }

            if (!in_array($profNode, $nodeIds)) {
                $nodeIds[] = $profNode;
                $nodes[] = ['id' => $profNode, 'name' => strip_tags($item->recommendation)];
            }


            $links[] = [
                'source' => $userNode,
                'target' => $profNode,
                'value' => $item->count
            ];
        }


        usort($links, fn($a, $b) => $b['value'] - $a['value']);
        $limitedLinks = array_slice($links, 0, 15);

        return [
            'nodes' => $nodes,
            'links' => $limitedLinks
        ];
    }

   
    public function timelineDistribution()
    {
        return Results::select(
            'user_id',
            DB::raw('MIN(DATE(created_at)) as first_test_date'),
            DB::raw('MAX(DATE(created_at)) as last_test_date')
        )
            ->with('user:id,login')
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                $firstDate = Carbon::parse($item->first_test_date);
                $lastDate = Carbon::parse($item->last_test_date);

                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->login ?? 'Пользователь #' . $item->user_id,
                    'first_date' => $firstDate->format('Y-m-d'),
                    'last_date' => $lastDate->format('Y-m-d'),
                    'start_day' => $firstDate->day,
                    'start_month' => $firstDate->month,
                    'start_year' => $firstDate->year,
                    'end_day' => $lastDate->day,
                    'end_month' => $lastDate->month,
                    'end_year' => $lastDate->year,
                    'duration_days' => $firstDate->diffInDays($lastDate)
                ];
            })
            ->toArray();
    }
}
