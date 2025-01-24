<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'industry',
        'individual_style_of_activity',
        'chess_structure',
        'result',
        'user_image_path',
        'match'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rectangles()
    {
        return $this->hasMany(Rectangle::class);
    }
}
