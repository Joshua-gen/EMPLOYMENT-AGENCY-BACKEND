<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Services\Website\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(protected JobService $jobService)
    {
    }

    // GET /api/website/jobs?search=&country=&page=&per_page=
    public function index(Request $request): JsonResponse
    {
        return $this->respond($this->jobService->index($request->only(['search', 'country', 'per_page'])));
    }

    // GET /api/website/jobs/{id}
    public function show(int $id): JsonResponse
    {
        return $this->respond($this->jobService->show($id));
    }

    private function respond(array $result): JsonResponse
    {
        return response()->json($result, $result['status'] ? 200 : 400);
    }
}
