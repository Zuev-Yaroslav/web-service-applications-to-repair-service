<?php

namespace App\Services\RequestRecordPanel;

use App\Enums\RequestRecordStatus;
use App\Http\Requests\RequestRecord\AssignRequest;
use App\Http\Requests\RequestRecord\UpdateStatusRequest;
use App\Models\RequestRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RequestRecordPanelService
{
    /**
     * @return array{requestRecords: \Illuminate\Support\Collection<int, array>, masters: Collection<int, User>|null, role: string, statusFilter: string|null}
     */
    public function getIndexData(Request $request): array
    {
        $user = $request->user()->load('role');
        $role = $user->role->name;

        $query = RequestRecord::query()->with(['assignedTo']);

        if ($role === 'dispatcher') {
            $query->filter($request->only('status'));
        } elseif ($role === 'master') {
            // Master sees assigned and in_progress requests assigned to them
            $query->where('assigned_to', $user->id)
                ->whereIn('status', [RequestRecordStatus::Assigned, RequestRecordStatus::InProgress]);
        }

        $requestRecords = $query->latest()->get();

        // Transform request records to ensure assigned_to is ID and assigned_to_user is the user object
        $transformedRecords = $requestRecords->map(function (RequestRecord $record) {
            $data = $record->toArray();
            // Ensure assigned_to is just the ID (not the relation)
            $data['assigned_to'] = $record->assigned_to;
            // Add assigned_to_user with user information
            $data['assigned_to_user'] = $record->assignedTo ? [
                'id' => $record->assignedTo->id,
                'name' => $record->assignedTo->name,
            ] : null;
            return $data;
        });

        $masters = null;
        if ($role === 'dispatcher') {
            $masterRole = Role::query()->where('name', 'master')->first();
            $masters = $masterRole ? User::query()->where('role_id', $masterRole->id)->get() : collect();
        }

        return [
            'requestRecords' => $transformedRecords,
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

    public function startWork(Request $request, RequestRecord $requestRecord): void
    {
        $user = $request->user();

        if ($requestRecord->status !== RequestRecordStatus::Assigned || $requestRecord->assigned_to !== $user->id) {
            abort(403, 'You can only start work on assigned requests assigned to you.');
        }

        $requestRecord->update([
            'status' => RequestRecordStatus::InProgress,
        ]);
    }

    public function finish(Request $request, RequestRecord $requestRecord): void
    {
        $user = $request->user();

        if ($requestRecord->status !== RequestRecordStatus::InProgress || $requestRecord->assigned_to !== $user->id) {
            abort(403, 'You can only finish requests that are in progress and assigned to you.');
        }

        $requestRecord->update([
            'status' => RequestRecordStatus::Done,
        ]);
    }
}
