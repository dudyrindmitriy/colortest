<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\Messages;
use App\Models\Newsletter;
use App\Models\NewsletterTopic;
use App\Models\Results;
use App\Models\User;
use App\Models\Review;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function indexUsers()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'login' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',

        ]);

        $user->login = $request->login;
        $user->email = $request->email;

        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'Пользователь успешно обновлён!');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Пользователь успешно удалён!');
    }


    public function indexResults()
    {
        $results = Results::with('user')->get();
        return view('admin.results.index', compact('results'));
    }

    public function editResult(Results $result)
    {
        $users = User::all();

        return view('admin.results.edit', compact('result', 'users'));
    }

    public function updateResult(Request $request, Results $result)
    {

        $result->update($request->only('user_id'));


        return redirect()->route('admin.results.index')->with('success', 'Результат успешно обновлён!');
    }

    public function destroyResult(Results $result)
    {
        $result->delete();
        return redirect()->route('admin.results.index')->with('success', 'Результат успешно удалён!');
    }
}
