<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Results extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'isa_id', 'industry', 'recommendation', 'user_image','chess_structure','chess_structure_id', 'match'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rectanglesForResult()
    {
        return $this->hasMany(RectanglesForResult::class);
    }
    public function isa()
    {
      return $this->belongsTo(Isa::class);
    }
    public function chess()
    {
      return $this->belongsTo(Chess::class);
    }
}
