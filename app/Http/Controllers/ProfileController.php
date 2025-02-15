<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TestResult;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        
        $user = Auth::user();
       
        return view('profile.index', compact('user'));
    }

    public function showPreviousResults()
    {
        
        $user = Auth::user();
        $results = $user->testResults;
        return view('profile.results', compact('results'));
    }
    public function update(Request $request)
    {
        
        $user = Auth::user();
    
        
        if (!$user) {
            return redirect('/login')->with('error', 'Вы не авторизованы!');
        }
    
        
        $user->name = $request->input('name');
        $user->email = $request->input('email');
    
        
        if (!($user instanceof \App\Models\User)) {
            return redirect()->back()->with('error', 'Ошибка: неверный объект пользователя.');
        }
    
        
        $user->save();
    
        return redirect()->route('profile')->with('success', 'Профиль обновлен!');
    }
}