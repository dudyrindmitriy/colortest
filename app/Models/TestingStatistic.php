<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestingStatistic extends Model
{
    protected $fillable = [
        'period_date',
        'tests_count', 
        'average_match',
        'new_users',
        'style_distribution'
    ];
    
    protected $casts = [
        'style_distribution' => 'array'
    ];
}
