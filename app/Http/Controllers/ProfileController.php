<?php

namespace App\Http\Controllers;

use App\Models\Messages;
use App\Models\NewsletterTopic;
use App\Models\Results;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TestResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $results = $user->results()->orderBy('created_at','desc')->get();
        return view('profile.index', compact('user', 'results'));
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
}
