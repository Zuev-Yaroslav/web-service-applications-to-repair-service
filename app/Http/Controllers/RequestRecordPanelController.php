<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestRecord\AssignRequest;
use App\Http\Requests\RequestRecord\UpdateStatusRequest;
use App\Models\RequestRecord;
use App\Services\RequestRecordPanel\RequestRecordPanelService;
use Illuminate\Http\JsonResponse;
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

    public function updateStatus(UpdateStatusRequest $request, RequestRecord $requestRecord): JsonResponse
    {
        $this->requestRecordPanelService->updateStatus($request, $requestRecord);

        return response()->json(['success' => true]);
    }

    public function assign(AssignRequest $request, RequestRecord $requestRecord): JsonResponse
    {
        $this->requestRecordPanelService->assign($request, $requestRecord);

        return response()->json(['success' => true]);
    }

    public function startWork(Request $request, RequestRecord $requestRecord): JsonResponse
    {
        if (! $this->requestRecordPanelService->startWork($request, $requestRecord)) {
            return response()->json(
                ['message' => 'Request already taken or no longer assigned to you.'],
                409
            );
        }

        return response()->json(['success' => true]);
    }

    public function finish(Request $request, RequestRecord $requestRecord): JsonResponse
    {
        $this->requestRecordPanelService->finish($request, $requestRecord);

        return response()->json(['success' => true]);
    }
}
