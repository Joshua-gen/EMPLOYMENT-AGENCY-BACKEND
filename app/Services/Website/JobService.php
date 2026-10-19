<?php

namespace App\Services\Website;

use App\Helpers\QueryResultHelper;
use App\Repositories\Website\JobRepository;
use Exception;

class JobService
{
    public function __construct(protected JobRepository $jobRepository)
    {
    }

    public function index(array $filters): array
    {
        try {
            $result = QueryResultHelper::onSuccessPaginated($this->jobRepository->paginate($filters));
            $result['countries'] = $this->jobRepository->countries();

            return $result;
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function show(int $id): array
    {
        try {
            $job = $this->jobRepository->find($id);

            if (! $job) {
                return QueryResultHelper::onCustomError('This job opening is no longer available.');
            }

            return QueryResultHelper::onSuccessGet($job);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }
}
