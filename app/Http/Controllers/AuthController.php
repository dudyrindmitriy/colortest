<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    
       
        $credentials = $request->only(['email', 'password']);
    
        if (Auth::attempt($credentials)) {
            return redirect()->route('home'); 
        }
    
        return back()->withErrors(['email' => 'Неправильный логин или пароль']);
    }

    public function showRegistrationForm()
    {
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
         
         $user = new User();
         $user->login = $request->input('login');
         $user->email = $request->input('email');
         $user->address = $request->input('address');
         $user->password = bcrypt($request->input('password'));
         $user->save();
         return redirect()->route('login');
    
    }

    public function logout()
    {
        
        Auth::logout();
        return redirect()->route('login');
    }
}
