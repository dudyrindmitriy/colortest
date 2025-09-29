<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;

trait Cloneable
{
    public function __clone()
    {
        foreach ($this->relations as $relationName => $relation) {
            if ($relation instanceof Collection) {
                $this->relations[$relationName] = $relation->map(function ($item) {
                    return clone $item;
                });
            }
        }
    }
}