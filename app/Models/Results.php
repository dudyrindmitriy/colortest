<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Results extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'user_image', 'match', 'ml_predictions', 'test_id', 'user_test_id', 'data',];

    protected $casts = [
        'ml_predictions' => 'array',
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function userTest()
    {
        return $this->belongsTo(UserTest::class);
    }

    public function rectanglesForResult()
    {
        return $this->hasMany(RectanglesForResult::class, 'result_id');
    }
}
