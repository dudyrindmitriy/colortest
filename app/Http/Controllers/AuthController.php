<?php

namespace App\Http\Controllers;

use App\Models\EducationProgram;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {

        return view('auth.login');
    }

    public function login(Request $request)
    {

        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);


        $user = User::where('login', $request->login)->first();

        // Проверяем пароль и логиним вручную
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('home');
        }
        return back()->withErrors('Неправильный логин или пароль');
    }

    public function showRegistrationForm()
    {
        $educationPrograms = EducationProgram::orderBy('name')->orderBy('code')->get();
        return view('auth.register', ['educationPrograms' => $educationPrograms]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'login' => 'required|string',
            // 'email'=>'nullable|string|unique:users',
            'user_type' => 'required',
            'password' => 'required|string',
            'education_program' => 'required_if:user_type, student|integer|exists:education_programs,id'
        ]);
        $user = new User();
        $user->login = $validated['login'];
        //  $user->email = $validated['email'] ?? null;
        $user->password = bcrypt($validated['password']);
        $user->user_type = $validated['user_type'];
        $user->education_program_id = $validated['user_type'] == 'student' ? $validated['education_program'] : null;
        if (User::where('login', $user->login)->first()) {
            return redirect()->back()->with('error', 'Пользователь с таким логином уже существует');
        } else {
            $user->save();
            Auth::login($user);
            return redirect()->route('home');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
