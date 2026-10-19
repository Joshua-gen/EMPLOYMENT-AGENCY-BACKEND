<?php

namespace App\Services\Management;

use App\Helpers\QueryResultHelper;
use App\Repositories\Management\JobRepository;
use Exception;

class JobService
{
    public function __construct(protected JobRepository $jobRepository)
    {
    }

    public function index(array $filters): array
    {
        try {
            return QueryResultHelper::onSuccessPaginated($this->jobRepository->paginate($filters));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function summary(): array
    {
        try {
            return QueryResultHelper::onSuccessGet($this->jobRepository->summary());
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function show(int $id): array
    {
        try {
            $job = $this->jobRepository->find($id);

            if (! $job) {
                return QueryResultHelper::onCustomError('Job opening not found');
            }

            return QueryResultHelper::onSuccessGet($job);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function store(array $data): array
    {
        try {
            $data['status'] = $data['status'] ?? 'Draft';

            return QueryResultHelper::onSuccessPost($this->jobRepository->create($data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $job = $this->jobRepository->find($id);

            if (! $job) {
                return QueryResultHelper::onCustomError('Job opening not found');
            }

            if (array_key_exists('employer_ids', $data)) {
                $inUse = $this->jobRepository->employersInUse($job, $data['employer_ids']);

                if ($inUse->isNotEmpty()) {
                    return QueryResultHelper::onCustomError(
                        'Cannot remove ' . $inUse->implode(', ') . ': applicants are assigned to it in this job opening.'
                    );
                }
            }

            return QueryResultHelper::onSuccessPut($this->jobRepository->update($job, $data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function destroy(int $id): array
    {
        try {
            $job = $this->jobRepository->find($id);

            if (! $job) {
                return QueryResultHelper::onCustomError('Job opening not found');
            }

            if ($job->applicants_count > 0) {
                return QueryResultHelper::onCustomError(
                    "Cannot delete \"{$job->title}\": it has {$job->applicants_count} applicant(s)."
                );
            }

            $this->jobRepository->delete($job);

            return QueryResultHelper::onSuccessDelete();
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }
}
