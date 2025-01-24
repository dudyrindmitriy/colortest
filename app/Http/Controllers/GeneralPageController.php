<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\Results;
use Illuminate\Http\Request;

class GeneralPageController extends Controller
{
    public function showGeneralPage()
    {
        $isas = Isa::all(); 
        $chess = Chess::all();
        return view('home', compact('isas','chess'));
    }
}
