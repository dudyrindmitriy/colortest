<?php

namespace App\Http\Controllers;

use App\Models\Isa;
use Illuminate\Http\Request;

class IsaController extends Controller
{
    public function show(Isa $isa)
    {
        $rectangles = $isa->rectanglesForIsa()
            ->get()
            ->groupBy('x'); // Группировка по стенам

        $wallNames = [
            1 => 'Левая стена',
            2 => 'Потолок',
            3 => 'Правая стена',
            4 => 'Пол'
        ];

        return view('isas.show', compact('isa', 'rectangles', 'wallNames'));
    }
}
