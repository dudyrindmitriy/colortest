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
            'email' => 'email|required',
            'user_type' => 'required',
            'password' => 'required|string',
            'education_program' => 'required_if:user_type, student|integer|exists:education_programs,id'
        ]);

        if (User::where('login', $validated['login'])->first()) {
            return redirect()->back()->with('error', 'Пользователь с таким логином уже существует');
        } else if (User::where('email', $validated['email'])->first()) {
            return redirect()->back()->with('error', 'Пользователь с таким email уже существует');
        } else {
            $user = new User();
            $user->login = $validated['login'];
            $user->email = $validated['email'];
            $user->password = bcrypt($validated['password']);
            $user->user_type = $validated['user_type'];
            $user->education_program_id = $validated['user_type'] == 'student' ? $validated['education_program'] : null;
            $user->save();
            Auth::login($user);
            return redirect()->route('home');
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'login' => 'required|string',
            'email' => 'email|required',
            'user_type' => 'required',
            'education_program' => 'required_if:user_type, student|nullable|integer|exists:education_programs,id'
        ]);


        if (User::where('login', $validated['login'])->where('id', '!=', $user->id)->first()) {
            return redirect()->back()->with('error', 'Пользователь с таким логином уже существует');
        } else if (User::where('email', $validated['email'])->where('id', '!=', $user->id)->first() && $validated['email']) {
            return redirect()->back()->with('error', 'Пользователь с таким email уже существует');
        } else {
            $user->login = $validated['login'];
            $user->email = $validated['email'];
            $user->user_type = $validated['user_type'];
            $user->education_program_id = $validated['user_type'] == 'student' ? $validated['education_program'] : null;
            $user->save();
        }

        return redirect()->back()->with('success', 'Профиль успешно обновлен');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
