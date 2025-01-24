<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\CommentToReview;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class CommentToReviewController extends Controller
{
    public function store(Request $request, $reviewId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:255', 
        ]);

        $comment = new CommentToReview();
        $comment->comment_text = $request->input('comment_text');
        $comment->review_id = $reviewId;
        $comment->user_id = Auth::id(); 
        $comment->save();

        return redirect()->route('reviews')->with('success', 'Комментарий успешно добавлен!');
    }
}