<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    
    public function newsletters()
    {
        return $this->hasMany(Newsletter::class, 'topic_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'newsletter_topic_user'); 
    }
}
