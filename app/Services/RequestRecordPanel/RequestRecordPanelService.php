<?php

namespace App\Services\RequestRecordPanel;

use App\Enums\RequestRecordStatus;
use App\Http\Requests\RequestRecord\AssignRequest;
use App\Http\Requests\RequestRecord\UpdateStatusRequest;
use App\Http\Resources\RequestRecordResource;
use App\Models\RequestRecord;
use Illuminate\Http\Request;

class RequestRecordPanelService
{
    public function __construct(
        private RequestRecordIndexQueryBuilder $indexQueryBuilder
    ) {}

    /**
     * @return array{requestRecords: \Illuminate\Http\Resources\Json\AnonymousResourceCollection, masters: \Illuminate\Support\Collection<int, \App\Models\User>|null, role: string, statusFilter: string|null}
     */
    public function getIndexData(Request $request): array
    {
        $user = $request->user()->load('role');
        $role = $user->role->name;

        $query = $this->indexQueryBuilder->build($request, $role);
        $requestRecords = $query->get();

        $masters = $role === 'dispatcher' ? $this->indexQueryBuilder->getMastersForDispatcher() : null;

        return [
            'requestRecords' => RequestRecordResource::collection($requestRecords)->resolve(),
            'masters' => $masters,
            'role' => $role,
            'statusFilter' => $request->get('status'),
        ];
    }

    public function updateStatus(UpdateStatusRequest $request, RequestRecord $requestRecord): void
    {
        $requestRecord->update([
            'status' => $request->validated()['status'],
        ]);
    }

    public function assign(AssignRequest $request, RequestRecord $requestRecord): void
    {
        $requestRecord->update([
            'assigned_to' => $request->validated()['master_id'],
            'status' => RequestRecordStatus::Assigned,
        ]);
    }

    public function startWork(Request $request, RequestRecord $requestRecord): bool
    {
        $user = $request->user();

        $updated = RequestRecord::query()
            ->where('id', $requestRecord->id)
            ->where('status', RequestRecordStatus::Assigned)
            ->where('assigned_to', $user->id)
            ->update(['status' => RequestRecordStatus::InProgress]);

        return $updated > 0;
    }

    public function finish(Request $request, RequestRecord $requestRecord): void
    {
        $requestRecord->update([
            'status' => RequestRecordStatus::Done,
        ]);
    }
}
