<?php

namespace App\Http\Controllers\Api\SystemDef;

use App\Http\Controllers\Controller;
use App\Http\Requests\Country\StoreCountryRequest;
use App\Http\Requests\Country\UpdateCountryRequest;
use App\Services\SystemDef\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct(protected CountryService $countryService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return $this->respond($this->countryService->index($request->only(['search', 'per_page'])));
    }

    // GET /api/management/countries/{id}
    public function show(int $id): JsonResponse
    {
        return $this->respond($this->countryService->show($id));
    }

    // POST /api/management/countries
    public function store(StoreCountryRequest $request): JsonResponse
    {
        return $this->respond($this->countryService->store($request->validated()), 201);
    }

    // PUT|PATCH /api/management/countries/{id}
    public function update(UpdateCountryRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->countryService->update($id, $request->validated()));
    }

    // DELETE /api/management/countries/{id}
    public function destroy(int $id): JsonResponse
    {
        return $this->respond($this->countryService->destroy($id));
    }

    private function respond(array $result, int $successCode = 200): JsonResponse
    {
        return response()->json($result, $result['status'] ? $successCode : 400);
    }
}
