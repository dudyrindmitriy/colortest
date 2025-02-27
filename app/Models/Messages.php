<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  
        'message',
        'recipient_id',
        'newsletter_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class, 'newsletter_id');
    }
}
