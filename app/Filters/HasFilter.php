<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

trait HasFilter
{
    public function scopeFilter(Builder $builder, $data) : Builder
    {
        $ClassName = "App\\Filters\\" . class_basename($this) . 'Filter';
//        return (new $ClassName())->apply($builder, $data);
        return app()->make($ClassName)->apply($builder, $data);
    }
}
