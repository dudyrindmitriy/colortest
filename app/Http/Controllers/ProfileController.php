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
        $topics = NewsletterTopic::all();
        $ut =  $user->topics;
        $subscribedTopics = $user->topics ? $user->topics->pluck('id')->toArray() : [];
        return view('profile.index', compact('user', 'topics', 'subscribedTopics'));
    }

    public function showResults()
    {
        $userId = Auth::id();

        $results = Results::where('user_id', $userId)->get();
        if (!$results) {
            return redirect()->route('profile.index')->with('error', 'Результаты не найдены');
        }
        return view('profile.results', compact('results'));
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
    public function search(Request $request)
    {
        $field = $request->input('field');
        $query = $request->input('query');


        $validFields = ['isa', 'chess_structure', 'created_at', 'recommendation'];

        if (!in_array($field, $validFields)) {
            return redirect()->route('results')->with('error', 'Неверное поле для поиска.');
        }


        $currentUserId = Auth::id();


        if ($field === 'chess_structure') {
            $results = Results::join('chesses', 'results.chess_structure_id', '=', 'chesses.id')
                ->where('results.user_id', $currentUserId)
                ->where('chesses.chess_structure', 'LIKE', "%{$query}%")
                ->select('results.*')
                ->get();
        } elseif ($field === 'isa') {
            $results = Results::join('isas', 'results.isa_id', '=', 'isas.id')
                ->where('results.user_id', $currentUserId)
                ->where('isas.individual_style_of_activity', 'LIKE', "%{$query}%")
                ->select('results.*')
                ->get();
        } else {
            $results = Results::where('user_id', $currentUserId)
                ->where($field, 'LIKE', "%{$query}%")
                ->get();
        }

        return view('profile.results', compact('results'));
    }
    public function sendMessage(Request $request)
    {
        try {
            $request->validate(["message" => "required|string"]);
            $user = Auth::user();
            $body = $request->message . ". Адрес отправителя - " . $user->email;
            $admins = User::where('isAdmin', 1)->get();
            $subject = "Новое сообщение от пользователя " . $user->login;
            $mailController = new PHPMailerController();
            foreach ($admins as $admin) {
                $mailController->send($admin->email, $subject, $body);
                $message = Messages::create([
                    'user_id' => $user->id,
                    'recipient_id' => $admin->id,
                    'message' => $body,
                ]);
                $message->save();
            }
            $mailController->send(env("MAIL_ADDRESS"), $subject, $body);
            return redirect()->back()->with("success", "Сообщение успешно отправлено");
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
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
    public function updateSubscriptions(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'topic_ids' => 'nullable|array',
                'topic_ids.*' => 'exists:newsletter_topics,id',
            ]);

            $topicIds = $request->input('topic_ids', []);

            $user->topics()->sync($topicIds);

            return redirect()->back()->with('success', 'Подписки обновлены.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
}
