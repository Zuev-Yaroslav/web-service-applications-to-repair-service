<?php

namespace App\Services\RecordRequest;

use App\Http\Requests\RequestRecord\StoreRequest;
use App\Models\RequestRecord;

class RecordRequestService
{
    public function store(StoreRequest $request): RequestRecord
    {
        return RequestRecord::query()->create($request->validated());
    }
}
