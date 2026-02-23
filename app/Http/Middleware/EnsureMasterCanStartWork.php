<?php

namespace App\Http\Middleware;

use App\Enums\RequestRecordStatus;
use App\Models\RequestRecord;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMasterCanStartWork
{
    /**
     * Allow master to change status to in_progress only if current status is assigned.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestRecord = $this->resolveRequestRecord($request);

        $user = $request->user();
        if (! $user || $user->loadMissing('role')->role?->name !== 'master') {
            abort(403, 'Unable to change status');
        }

        if ($requestRecord->status !== RequestRecordStatus::Assigned) {
            abort(403, 'Unable to change status');
        }

        if ($requestRecord->assigned_to !== $user->id) {
            abort(403, 'Unable to change status');
        }

        return $next($request);
    }

    private function resolveRequestRecord(Request $request): RequestRecord
    {
        $param = $request->route('requestRecord');

        return $param instanceof RequestRecord
            ? $param
            : RequestRecord::findOrFail($param);
    }
}
