<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        
        $reviews = Review::all();
        return view('reviews.index', compact('reviews'));
    }

    public function create()
    {
        
        return view('reviews.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'review_text' => 'required|string|max:255',
            'rating' => 'required|integer|between:1,5',
        ]);

        $review = new Review();
        $review->review_text = $request->input('review_text');
        $review->rating = $request->input('rating');
        $review->user_id = Auth::id();
        $review->save();

        return redirect()->route('reviews')->with('success', 'Отзыв успешно добавлен!');
    }
    public function search(Request $request)
    {
        $query = $request->input('query');

        
        $reviews = Review::where('review_text', 'LIKE', "%{$query}%")
                     ->orWhere('rating', $query) 
                     ->with('user') 
                     ->get();

        return view('reviews.index', compact('reviews'));
    }
}