<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreJobRequest;
use App\Http\Requests\Management\UpdateJobRequest;
use App\Services\Management\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(protected JobService $jobService)
    {
    }

    // GET /api/management/jobs?search=&status=&page=&per_page=
    // GET /api/management/jobs?search=&status=&country=&page=&per_page=
    public function index(Request $request): JsonResponse
    {
        return $this->respond($this->jobService->index($request->only(['search', 'status', 'country', 'per_page'])));
    }

    // GET /api/management/jobs/summary
    public function summary(): JsonResponse
    {
        return $this->respond($this->jobService->summary());
    }

    // GET /api/management/jobs/{id}
    public function show(int $id): JsonResponse
    {
        return $this->respond($this->jobService->show($id));
    }

    // POST /api/management/jobs
    public function store(StoreJobRequest $request): JsonResponse
    {
        return $this->respond($this->jobService->store($request->validated()), 201);
    }

    // PUT|PATCH /api/management/jobs/{id}
    public function update(UpdateJobRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->jobService->update($id, $request->validated()));
    }

    // DELETE /api/management/jobs/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->respond($this->jobService->destroy($id));
    }

    private function respond(array $result, int $successCode = 200): JsonResponse
    {
        return response()->json($result, $result['status'] ? $successCode : 400);
    }
}
