<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestRecord\StoreRequest;
use App\Services\RecordRequest\RecordRequestService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RequestRecordController extends Controller
{
    public function __construct(
        private RecordRequestService $recordRequestService
    ) {}

    public function create(): Response
    {
        return Inertia::render('request-record/RequestRecordCreate', [
            'status' => session('status'),
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->recordRequestService->store($request);

        return back()->with('status', 'Created');
    }
}
