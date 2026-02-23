<?php

namespace App\Services\RequestRecordPanel;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RequestRecordIndexQueryBuilder
{
    private const MASTER_ALLOWED_STATUSES = [
        RequestRecordStatus::Assigned,
        RequestRecordStatus::InProgress,
        RequestRecordStatus::Done,
    ];

    public function build(Request $request, string $role): Builder
    {
        $query = RequestRecord::query()->with(['assignedTo']);

        if ($role === 'dispatcher') {
            $query->filter($request->only('status'));
        } elseif ($role === 'master') {
            $user = $request->user();
            $query->where('assigned_to', $user->id)
                ->whereIn('status', self::MASTER_ALLOWED_STATUSES);

            $status = RequestRecordStatus::tryFrom((string) $request->get('status'));
            if ($status !== null && in_array($status, self::MASTER_ALLOWED_STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        return $query->latest('created_at')->latest('id');
    }

    /**
     * @return Collection<int, User>
     */
    public function getMastersForDispatcher(): Collection
    {
        $masterRole = Role::query()->where('name', 'master')->first();

        return $masterRole
            ? User::query()->where('role_id', $masterRole->id)->get()
            : collect();
    }
}
