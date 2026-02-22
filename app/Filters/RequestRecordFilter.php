<?php

namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;

class RequestRecordFilter extends AbstractFilter
{
    protected array $keys = [
        'status',
    ];
    protected function status(Builder $builder, $value)
    {
        $builder->where('status', $value);
    }
}
