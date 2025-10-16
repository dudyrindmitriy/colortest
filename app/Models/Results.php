<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Results extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'user_image','match'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rectanglesForResult()
    {
        return $this->hasMany(RectanglesForResult::class, 'result_id');
    }
}
