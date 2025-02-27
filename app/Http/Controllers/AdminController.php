<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\Messages;
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
        $messages = Messages::where("recipient_id", Auth::id())->get();

        return view('admin.index', compact('messages'));
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

    public function indexReviews()
    {
        $reviews = Review::with('user')->get();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function editReview(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function updateReview(Request $request, Review $review)
    {
        $request->validate([
            'review_text' => 'required|string|max:255',
            'rating' => 'required|integer|between:1,5',
        ]);

        $review->update($request->only('review_text', 'rating'));
        return redirect()->route('admin.reviews.index')->with('success', 'Отзыв успешно обновлён!');
    }

    public function destroyReview(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Отзыв успешно удалён!');
    }
    public function indexResults()
    {
        $results = Results::with('user')->get();
        return view('admin.results.index', compact('results'));
    }

    public function editResult(Results $result)
    {
        $users = User::all();
        $isas = Isa::all();
        $chessStructures = Chess::all();

        return view('admin.results.edit', compact('result', 'users', 'isas', 'chessStructures'));
    }

    public function updateResult(Request $request, Results $result)
    {
        $isa = Isa::find($request->input('isa_id'));
        $chess = Chess::find($request->input('chess_structure_id'));

        $industry = $isa ? $isa->individual_style_of_activity : null;
        $chess_structure = $chess ? $chess->chess_structure : null;

        $result->update($request->only('user_id', 'isa_id', 'chess_structure_id', 'recommendation') + [
            'industry' => $industry,
            'chess_structure' => $chess_structure
        ]);


        return redirect()->route('admin.results.index')->with('success', 'Результат успешно обновлён!');
    }

    public function destroyResult(Results $result)
    {
        $result->delete();
        return redirect()->route('admin.results.index')->with('success', 'Результат успешно удалён!');
    }
    public function replyMessage(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'message' => 'required|string'
            ]);
            $message = Messages::find($request->id);
            $user = $message->user;
            $subject = "Ответ на вопрос на сайте colortest.ru";
            $body = "Ваш вопрос: <br>" . $message->message . "<br><br> Ответ: <br>" . $request->message;
            $mailController = new PHPMailerController;
            $mailController->send($user->email, $subject, $body);
            $message->delete();
            $newMessage = Messages::create([
                'user_id' => Auth::id(),
                'recipient_id' => $user->id,
                'message' => $body,
            ]);
            $newMessage->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => true]);
        }
    }
    public function showMessageForm(User $user)
    {
        return view('admin.users.message', ['user' => $user]);
    }
    public function sendMessage(Request $request, User $user)
    {
        try {
            $request->validate([
                'message' => 'required|string'
            ]);
            $subject = "Сообщение от администратора сайта colortest.ru";
            $body = $request->message;
            $mailController = new PHPMailerController;
            $admin = User::find(Auth::id());
            $mailController->send($user->email, $subject, $body);
            $message = Messages::create([
                'user_id' => $admin->id,
                'recipient_id' => $user->id,
                'message' => $body,
            ]);
            $message->save();
            return redirect()->back()->with('success', 'Сообщение успешно отправлено');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
}
