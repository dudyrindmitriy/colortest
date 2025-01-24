<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RectanglesForIsa extends Model
{
    use HasFactory;

    protected $fillable = ['isa_id', 'color', 'x', 'y', 'z'];

    public function isa()
    {
        return $this->belongsTo(Isa::class, 'isa_id');
    }
}
