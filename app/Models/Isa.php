<?php

namespace App\Models;

use App\Traits\Cloneable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Isa extends Model
{
    use Cloneable;
    use HasFactory;

    protected $fillable = ['individual_style_of_activity', 'image_path','image'];

    public function rectanglesForIsa()
    {
        return $this->hasMany(RectanglesForIsa::class, 'isa_id');
    }
}
