<?php

namespace App\Http\Controllers;

use App\Enums\RequestRecordStatus;
use App\Http\Requests\RequestRecord\AssignRequest;
use App\Http\Requests\RequestRecord\UpdateStatusRequest;
use App\Models\RequestRecord;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RequestRecordPanelController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user()->load('role');
        $role = $user->role->name;
        
        $query = RequestRecord::query()->with(['assignedTo']);
        
        if ($role === 'dispatcher') {
            // Dispatcher sees all requests
            $query->when($request->has('status'), function ($q) use ($request) {
                $q->where('status', $request->get('status'));
            });
        } elseif ($role === 'master') {
            // Master sees assigned and in_progress requests assigned to them
            $query->where('assigned_to', $user->id)
                ->whereIn('status', [RequestRecordStatus::Assigned, RequestRecordStatus::InProgress]);
        }
        
        $requestRecords = $query->latest()->get();
        
        $masters = null;
        if ($role === 'dispatcher') {
            $masterRole = Role::query()->where('name', 'master')->first();
            $masters = $masterRole ? User::query()->where('role_id', $masterRole->id)->get() : collect();
        }
        
        return Inertia::render('request-record/RequestRecordPanel', [
            'requestRecords' => $requestRecords,
            'masters' => $masters,
            'role' => $role,
            'statusFilter' => $request->get('status'),
        ]);
    }
    
    public function updateStatus(UpdateStatusRequest $request, RequestRecord $requestRecord): RedirectResponse
    {
        $requestRecord->update([
            'status' => $request->validated()['status'],
        ]);
        
        return back();
    }
    
    public function assign(AssignRequest $request, RequestRecord $requestRecord): RedirectResponse
    {
        $requestRecord->update([
            'assigned_to' => $request->validated()['master_id'],
            'status' => RequestRecordStatus::Assigned,
        ]);
        
        return back();
    }
    
    public function startWork(Request $request, RequestRecord $requestRecord): RedirectResponse
    {
        $user = $request->user();
        
        if ($requestRecord->status !== RequestRecordStatus::Assigned || $requestRecord->assigned_to !== $user->id) {
            abort(403, 'You can only start work on assigned requests assigned to you.');
        }
        
        $requestRecord->update([
            'status' => RequestRecordStatus::InProgress,
        ]);
        
        return back()->with('status', 'Work started');
    }
    
    public function finish(Request $request, RequestRecord $requestRecord): RedirectResponse
    {
        $user = $request->user();
        
        if ($requestRecord->status !== RequestRecordStatus::InProgress || $requestRecord->assigned_to !== $user->id) {
            abort(403, 'You can only finish requests that are in progress and assigned to you.');
        }
        
        $requestRecord->update([
            'status' => RequestRecordStatus::Done,
        ]);
        
        return back()->with('status', 'Request finished');
    }
}
