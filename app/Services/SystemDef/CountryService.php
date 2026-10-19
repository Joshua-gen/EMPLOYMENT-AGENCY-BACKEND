<?php

namespace App\Services\SystemDef;

use App\Helpers\QueryResultHelper;
use App\Repositories\SystemDef\CountryRepository;
use Exception;

class CountryService
{
    public function __construct(protected CountryRepository $countryRepository)
    {
    }

    // Walang per_page -> lahat ng countries. May per_page -> paginated.
    public function index(array $filters): array
    {
        try {
            if (empty($filters['per_page'])) {
                return QueryResultHelper::onSuccessGet($this->countryRepository->all($filters));
            }

            return QueryResultHelper::onSuccessPaginated($this->countryRepository->paginate($filters));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function show(int $id): array
    {
        try {
            $country = $this->countryRepository->find($id);

            if (! $country) {
                return QueryResultHelper::onCustomError('Country not found');
            }

            return QueryResultHelper::onSuccessGet($country);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function store(array $data): array
    {
        try {
            return QueryResultHelper::onSuccessPost($this->countryRepository->create($data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $country = $this->countryRepository->find($id);

            if (! $country) {
                return QueryResultHelper::onCustomError('Country not found');
            }

            return QueryResultHelper::onSuccessPut($this->countryRepository->update($country, $data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function destroy(int $id): array
    {
        try {
            $country = $this->countryRepository->find($id);

            if (! $country) {
                return QueryResultHelper::onCustomError('Country not found');
            }

            $this->countryRepository->delete($country);

            return QueryResultHelper::onSuccessDelete();
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }
}
