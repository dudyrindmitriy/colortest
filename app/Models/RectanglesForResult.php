<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RectanglesForResult extends Model
{
    use HasFactory;

    protected $fillable = ['result_id', 'color', 'x', 'y', 'z'];

    public function isa()
    {
        return $this->belongsTo(Isa::class);
    }
}
