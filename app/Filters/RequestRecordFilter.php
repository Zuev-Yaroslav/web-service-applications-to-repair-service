<?php

namespace App\Filters;

use App\Enums\RequestRecordStatus;
use Illuminate\Database\Eloquent\Builder;

class RequestRecordFilter extends AbstractFilter
{
    protected array $keys = [
        'status',
    ];

    protected function status(Builder $builder, mixed $value): void
    {
        $status = RequestRecordStatus::tryFrom((string) $value);
        if ($status === null) {
            return;
        }
        $builder->where('status', $status);
    }
}
