<?php

namespace App\Services\Management;

use App\Helpers\QueryResultHelper;
use App\Repositories\Management\JobRepository;
use App\Repositories\Management\ManpowerRequestRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ManpowerRequestReceived;
use Throwable;
use Exception;

class ManpowerRequestService
{
    public function __construct(
        protected ManpowerRequestRepository $manpowerRequestRepository,
        protected JobRepository $jobRepository
    ) {
    }

    public function index(array $filters): array
    {
        try {
            return QueryResultHelper::onSuccessPaginated($this->manpowerRequestRepository->paginate($filters));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function summary(): array
    {
        try {
            return QueryResultHelper::onSuccessGet($this->manpowerRequestRepository->summary());
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function show(int $id): array
    {
        try {
            $request = $this->manpowerRequestRepository->find($id);

            if (! $request) {
                return QueryResultHelper::onCustomError('Manpower request not found');
            }

            // Hint kay admin kung may kahawig nang employer
            if ($request->status === 'Pending') {
                $request->setRelation('possible_employers', $this->manpowerRequestRepository->possibleEmployers($request));
            }

            return QueryResultHelper::onSuccessGet($request);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // Public submit
    public function store(array $data): array
    {
        try {
            $request = $this->manpowerRequestRepository->create($data);

            $this->sendConfirmation($request);

            return QueryResultHelper::onSuccessPost($request);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    private function sendConfirmation($request): void
    {
        try {
            Mail::to($request->contact_email)
                ->send(new ManpowerRequestReceived($request->loadMissing('positions')));
        } catch (Throwable $e) {
            Log::warning('Manpower request email failed', [
                'reference' => $request->reference,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    // Link sa existing employer o gumawa ng bago. Hindi gumagawa ng job.
    public function approve(int $id, array $data, ?int $userId): array
    {
        try {
            $request = $this->manpowerRequestRepository->find($id);

            if (! $request) {
                return QueryResultHelper::onCustomError('Manpower request not found');
            }

            if ($request->status !== 'Pending') {
                return QueryResultHelper::onCustomError('Only pending requests can be approved.');
            }

            if (! empty($data['employer_id'])) {
                $employer = $this->manpowerRequestRepository->findEmployer((int) $data['employer_id']);

                if ($employer->country !== $request->country) {
                    return QueryResultHelper::onCustomError(
                        "{$employer->name} is in {$employer->country}, but this request is for {$request->country}."
                    );
                }
            } else {
                $duplicate = $this->manpowerRequestRepository->duplicateEmployer($request);

                if ($duplicate) {
                    return QueryResultHelper::onCustomError(
                        "An employer with the same name or license number already exists ({$duplicate->name}). Link the request to that employer instead."
                    );
                }

                $employer = $this->manpowerRequestRepository->createEmployerFrom($request);
            }

            return QueryResultHelper::onSuccessPut(
                $this->manpowerRequestRepository->approve($request, $employer, $data['remarks'] ?? null, $userId)
            );
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function reject(int $id, string $remarks, ?int $userId): array
    {
        try {
            $request = $this->manpowerRequestRepository->find($id);

            if (! $request) {
                return QueryResultHelper::onCustomError('Manpower request not found');
            }

            if ($request->status !== 'Pending') {
                return QueryResultHelper::onCustomError('Only pending requests can be rejected.');
            }

            return QueryResultHelper::onSuccessPut($this->manpowerRequestRepository->reject($request, $remarks, $userId));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function linkJob(int $id, int $positionId, int $jobId): array
    {
        try {
            $request = $this->manpowerRequestRepository->find($id);

            if (! $request) {
                return QueryResultHelper::onCustomError('Manpower request not found');
            }

            if ($request->status !== 'Approved') {
                return QueryResultHelper::onCustomError('Approve the request before linking its positions to a job.');
            }

            $position = $request->positions->firstWhere('id', $positionId);

            if (! $position) {
                return QueryResultHelper::onCustomError('Position not found in this request.');
            }

            if ($position->job_opening_id) {
                return QueryResultHelper::onCustomError('This position is already linked to a job opening.');
            }

            $job = $this->jobRepository->find($jobId);

            if (! $job) {
                return QueryResultHelper::onCustomError('Job opening not found');
            }

            if ($job->status === 'Closed') {
                return QueryResultHelper::onCustomError('Closed job openings cannot be linked.');
            }

            if ($job->country !== $request->country) {
                return QueryResultHelper::onCustomError(
                    "This job opening is in {$job->country}, but the request is for {$request->country}."
                );
            }

            return QueryResultHelper::onSuccessPut(
                $this->manpowerRequestRepository->linkPosition($request, $position, $job)
            );
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function unlinkJob(int $id, int $positionId): array
    {
        try {
            $request = $this->manpowerRequestRepository->find($id);

            if (! $request) {
                return QueryResultHelper::onCustomError('Manpower request not found');
            }

            $position = $request->positions->firstWhere('id', $positionId);

            if (! $position) {
                return QueryResultHelper::onCustomError('Position not found in this request.');
            }

            if (! $position->job_opening_id) {
                return QueryResultHelper::onCustomError('This position is not linked to a job opening.');
            }

            return QueryResultHelper::onSuccessPut(
                $this->manpowerRequestRepository->unlinkPosition($request, $position)
            );
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }
}
