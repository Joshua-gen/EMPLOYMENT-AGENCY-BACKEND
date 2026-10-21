<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\ApproveManpowerRequestRequest;
use App\Http\Requests\Management\LinkManpowerPositionJobRequest;
use App\Http\Requests\Management\RejectManpowerRequestRequest;
use App\Services\Management\ManpowerRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManpowerRequestController extends Controller
{
    public function __construct(protected ManpowerRequestService $manpowerRequestService)
    {
    }

    // GET /api/management/manpower-requests?search=&status=&country=&page=&per_page=
    public function index(Request $request): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->index(
            $request->only(['search', 'status', 'country', 'per_page'])
        ));
    }

    // GET /api/management/manpower-requests/summary
    public function summary(): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->summary());
    }

    // GET /api/management/manpower-requests/{id}
    public function show(int $id): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->show($id));
    }

    // PATCH /api/management/manpower-requests/{id}/approve   { employer_id } o { create_employer: true }
    public function approve(ApproveManpowerRequestRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->approve($id, $request->validated(), $request->user()?->id));
    }

    // PATCH /api/management/manpower-requests/{id}/reject   { remarks }
    public function reject(RejectManpowerRequestRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->reject(
            $id,
            $request->validated('remarks'),
            $request->user()?->id
        ));
    }

    // PATCH /api/management/manpower-requests/{id}/positions/{position}/link-job   { job_opening_id }
    public function linkJob(LinkManpowerPositionJobRequest $request, int $id, int $position): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->linkJob(
            $id,
            $position,
            (int) $request->validated('job_opening_id')
        ));
    }

    // DELETE /api/management/manpower-requests/{id}/positions/{position}/link-job
    public function unlinkJob(int $id, int $position): JsonResponse
    {
        return $this->respond($this->manpowerRequestService->unlinkJob($id, $position));
    }

    private function respond(array $result, int $successCode = 200): JsonResponse
    {
        return response()->json($result, $result['status'] ? $successCode : 400);
    }
}
