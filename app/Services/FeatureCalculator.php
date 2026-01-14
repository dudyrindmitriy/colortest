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
        $symmetryMetrics = [
            'vertical_symmetry' => $instance->calculateVerticalSymmetry($walls[1] ?? [], $walls[3] ?? []),
            'horizontal_symmetry' => $instance->calculateHorizontalSymmetry($walls[2] ?? [], $walls[4] ?? [])
        ];
        $contrastMetrics = [
            'vertical_contrast' => $instance->calculateVerticalContrast($walls),
            'horizontal_contrast' => $instance->calculateHorizontalContrast($walls),
            'depth_contrast' => $instance->calculateDepthContrast($walls)
        ];
        $patternMetrics = $instance->calculatePatternMetrics($walls);

        return array_merge(
            $features,
            $symmetryMetrics,
            $contrastMetrics,
            $patternMetrics
        );
    }
}
