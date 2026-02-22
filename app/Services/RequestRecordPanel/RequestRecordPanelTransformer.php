<?php

namespace App\Services\RequestRecordPanel;

use App\Models\RequestRecord;
use Illuminate\Support\Collection;

class RequestRecordPanelTransformer
{
    /**
     * @param  Collection<int, RequestRecord>  $records
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function transformForIndex(Collection $records): Collection
    {
        return $records->map(fn (RequestRecord $record) => [
            ...$record->toArray(),
            'assigned_to' => $record->assigned_to,
            'assigned_to_user' => $record->assignedTo ? [
                'id' => $record->assignedTo->id,
                'email' => $record->email,
                'name' => $record->assignedTo->name,
            ] : null,
        ]);
    }
}
