<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use App\Services\Management\BusinessCategoryService;
use Illuminate\Http\JsonResponse;

class BusinessCategoryController extends Controller
{
    public function __construct(protected BusinessCategoryService $businessCategoryService)
    {
    }

    // GET /api/management/business-categories
    public function index(): JsonResponse
    {
        $result = $this->businessCategoryService->index();

        return response()->json($result, $result['status'] ? 200 : 400);
    }
}
