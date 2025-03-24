<?php

namespace App\Http\Controllers;

use App\Models\ForumMessage;
use App\Models\ForumReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index()
    {
        $messages = ForumMessage::with(['user', 'reactions.user'])
            ->whereNull('parent_id')
            ->latest()
            ->paginate(10);

        return view('forum.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required|string|max:2000']);

        ForumMessage::create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Сообщение отправлено!');
    }

    public function react(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:forum_messages,id',
            'type' => 'required|in:like,dislike'
        ]);

        ForumReaction::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'message_id' => $request->message_id
            ],
            ['type' => $request->type]
        );

        return back();
    }

    public function removeReaction($messageId)
    {
        ForumReaction::where('user_id', Auth::id())
            ->where('message_id', $messageId)
            ->delete();

        return back();
    }
}