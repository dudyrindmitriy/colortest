<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    public function comments()
    {
        return $this->hasMany(CommentToReview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $fillable = ['review_text', 'rating', 'user_id'];
}