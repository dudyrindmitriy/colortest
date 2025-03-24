<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumReaction extends Model
{
    protected $fillable = ['message_id', 'user_id', 'type'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ForumMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
