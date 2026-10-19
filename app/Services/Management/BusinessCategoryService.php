<?php

namespace App\Services\Management;

use App\Helpers\QueryResultHelper;
use App\Repositories\Management\BusinessCategoryRepository;
use Exception;

class BusinessCategoryService
{
    public function __construct(protected BusinessCategoryRepository $businessCategoryRepository)
    {
    }

    public function index(): array
    {
        try {
            return QueryResultHelper::onSuccessGet($this->businessCategoryRepository->all());
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }
}
