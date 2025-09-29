<?php

namespace App\Services;

use App\Models\Isa;
use App\Console\Commands\ExportTrainingData;

class FeatureCalculator extends ExportTrainingData
{
    public static function calculate($rectangles)
    {
        $instance = new self();
        
        $walls = $instance->groupRectanglesByWall($rectangles);
        $features = [];
        
        foreach (self::WALL_MAP as $x => $wallName) {
            $wallRects = $walls[$x] ?? [];
            $features += $instance->processWall($wallName, $wallRects);
        }
        
        $patternMetrics = $instance->calculatePatternMetrics($walls);
        
        return array_merge($features, $patternMetrics);
    }
}