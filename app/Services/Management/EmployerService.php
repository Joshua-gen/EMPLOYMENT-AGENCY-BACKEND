<?php

namespace App\Services\Management;

use App\Helpers\QueryResultHelper;
use App\Repositories\Management\EmployerRepository;
use Exception;

class EmployerService
{
    public function __construct(protected EmployerRepository $employerRepository)
    {
    }

    public function index(array $filters): array
    {
        try {
            return QueryResultHelper::onSuccessPaginated($this->employerRepository->paginate($filters));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function show(int $id): array
    {
        try {
            $employer = $this->employerRepository->find($id);

            if (! $employer) {
                return QueryResultHelper::onCustomError('Employer not found');
            }

            return QueryResultHelper::onSuccessGet($employer);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function store(array $data): array
    {
        try {
            return QueryResultHelper::onSuccessPost($this->employerRepository->create($data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $employer = $this->employerRepository->find($id);

            if (! $employer) {
                return QueryResultHelper::onCustomError('Employer not found');
            }

            return QueryResultHelper::onSuccessPut($this->employerRepository->update($employer, $data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function destroy(int $id): array
    {
        try {
            $employer = $this->employerRepository->find($id);

            if (! $employer) {
                return QueryResultHelper::onCustomError('Employer not found');
            }

            if ($employer->job_openings_count > 0) {
                return QueryResultHelper::onCustomError(
                    "Cannot delete \"{$employer->name}\": it is used by {$employer->job_openings_count} job opening(s)."
                );
            }

            if ($employer->manpower_requests_count > 0) {
                return QueryResultHelper::onCustomError(
                    "Cannot delete \"{$employer->name}\": it has {$employer->manpower_requests_count} manpower request(s)."
                );
            }

            $this->employerRepository->delete($employer);

            return QueryResultHelper::onSuccessDelete();
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }
}
