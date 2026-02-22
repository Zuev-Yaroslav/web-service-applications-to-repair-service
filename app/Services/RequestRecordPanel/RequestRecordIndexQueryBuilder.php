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
    public function build(Request $request, string $role): Builder
    {
        $query = RequestRecord::query()->with(['assignedTo']);

        if ($role === 'dispatcher') {
            $query->filter($request->only('status'));
        } elseif ($role === 'master') {
            $user = $request->user();
            $query->where('assigned_to', $user->id)
                ->whereIn('status', [RequestRecordStatus::Assigned, RequestRecordStatus::InProgress]);
        }

        return $query->latest();
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
