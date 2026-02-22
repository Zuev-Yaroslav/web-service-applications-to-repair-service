<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestRecord\AssignRequest;
use App\Http\Requests\RequestRecord\UpdateStatusRequest;
use App\Models\RequestRecord;
use App\Services\RequestRecordPanel\RequestRecordPanelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RequestRecordPanelController extends Controller
{
    public function __construct(
        private RequestRecordPanelService $requestRecordPanelService
    ) {}

    public function index(Request $request): Response
    {
        $data = $this->requestRecordPanelService->getIndexData($request);

        return Inertia::render('request-record/RequestRecordPanel', $data);
    }

    public function updateStatus(UpdateStatusRequest $request, RequestRecord $requestRecord): RedirectResponse
    {
        $this->requestRecordPanelService->updateStatus($request, $requestRecord);

        return back();
    }

    public function assign(AssignRequest $request, RequestRecord $requestRecord): RedirectResponse
    {
        $this->requestRecordPanelService->assign($request, $requestRecord);

        return back();
    }

    public function startWork(Request $request, RequestRecord $requestRecord): RedirectResponse
    {
        $this->requestRecordPanelService->startWork($request, $requestRecord);

        return back()->with('status', 'Work started');
    }

    public function finish(Request $request, RequestRecord $requestRecord): RedirectResponse
    {
        $this->requestRecordPanelService->finish($request, $requestRecord);

        return back()->with('status', 'Request finished');
    }
}
