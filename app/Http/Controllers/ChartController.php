<?php

namespace App\Http\Controllers;

use App\Models\Results;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChartController extends Controller
{
    public function isaDistribution()
    {
        return Results::with('isa')
            ->selectRaw('isa_id, count(*) as count')
            ->groupBy('isa_id')
            ->get()
            ->map(function($item) {
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
            ->map(function($item) {
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
}
