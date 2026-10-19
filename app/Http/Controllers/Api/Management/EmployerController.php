<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\StoreEmployerRequest;
use App\Http\Requests\Management\UpdateEmployerRequest;
use App\Services\Management\EmployerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    public function __construct(protected EmployerService $employerService)
    {
    }

    // GET /api/management/employers?search=&country=&business_category_id=&page=&per_page=
    public function index(Request $request): JsonResponse
    {
        return $this->respond(
            $this->employerService->index($request->only(['search', 'country', 'business_category_id', 'per_page']))
        );
    }

    // GET /api/management/employers/{id}
    public function show(int $id): JsonResponse
    {
        return $this->respond($this->employerService->show($id));
    }

    // POST /api/management/employers
    public function store(StoreEmployerRequest $request): JsonResponse
    {
        return $this->respond($this->employerService->store($request->validated()), 201);
    }

    // PUT|PATCH /api/management/employers/{id}
    public function update(UpdateEmployerRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->employerService->update($id, $request->validated()));
    }

    // DELETE /api/management/employers/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->respond($this->employerService->destroy($id));
    }

    private function respond(array $result, int $successCode = 200): JsonResponse
    {
        return response()->json($result, $result['status'] ? $successCode : 400);
    }
}
