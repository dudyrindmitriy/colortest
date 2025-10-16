<?php
namespace App\Http\Controllers;

use App\Models\EducationProgram;
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
        $educationPrograms = EducationProgram::orderBy('name')->orderBy('code')->get();
        return view('auth.register',['educationPrograms'=>$educationPrograms]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'login'=>'required|string',
            'email'=>'required|string',
            'user_type'=>'required',
            'password'=>'required|string',
            'education_program'=>'required_if:user_type, student|integer|exists:education_programs,id'
        ]);
         $user = new User();
         $user->login = $validated['login'];
         $user->email = $validated['email'];
         $user->password = bcrypt($validated['password']);
         $user->user_type = $validated['user_type'];
         $user->education_program_id = $validated['user_type'] == 'student' ? $validated['education_program'] : null;
        if (User::where('email', $user->email)->first()) {
            return redirect()->back()->with('error', 'Пользователь с таким email уже существует');
        } else {
            $user->save();
            return redirect()->route('login');
        }

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
