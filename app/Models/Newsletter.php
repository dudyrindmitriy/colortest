<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'text',
        'created_at'
    ];

   
    public function topic()
    {
        return $this->belongsTo(NewsletterTopic::class, 'topic_id');
    }

    
    public function messages()
    {
        return $this->hasMany(Messages::class, 'newsletter_id');
    }

   
}
