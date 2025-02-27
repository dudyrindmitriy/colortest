<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\NewsletterTopic;
use App\Models\Messages;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function create()
    {
        $newslettertopics = NewsletterTopic::all();
        return view('newsletter.create', compact('newslettertopics'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'topic_id' => 'required|exists:newsletter_topics,id',
                'text' => 'required|string|min:1',
            ]);

            $topicId = $request->input('topic_id');
            $text = $request->input('text');
            $topic = NewsletterTopic::find($topicId);
            $subscribers = $topic->users;

            $newsletter = Newsletter::create([
                'topic_id' => $topicId,
                'text' => $text,
            ]);
            $newsletter->save();
            // $message = Messages::create([
            //     'newsletter_id' => $newsletter->id,
            //     'text' => $text,
            // ]);
            $user = Auth::user();
            $body = $text;
            $subject = "Сообщение по рассылке  " . $topic->name;
            $mailController = new PHPMailerController();
            foreach ($subscribers as $subscriber) {
                $mailController->send($subscriber->email, $subject, $body);
                $message = Messages::create([
                    'user_id' => $user->id,
                    'recipient_id' => $subscriber->id,
                    'message' => $body,
                    'newsletter_id' => $newsletter->id
                ]);
                $message->save();
            }



            return redirect()->back()->with('success', 'Рассылка успешно отправлена!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }
}
