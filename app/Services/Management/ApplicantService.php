<?php

namespace App\Services\Management;

use App\Helpers\QueryResultHelper;
use App\Models\Management\Applicant;
use App\Repositories\Management\ApplicantRepository;
use App\Repositories\Management\JobRepository;
use App\Mail\ApplicationReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Exception;

class ApplicantService
{
    public function __construct(
        protected ApplicantRepository $applicantRepository,
        protected JobRepository $jobRepository
    ) {
    }

    public function index(array $filters): array
    {
        try {
            return QueryResultHelper::onSuccessPaginated($this->applicantRepository->paginate($filters));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function summary(): array
    {
        try {
            return QueryResultHelper::onSuccessGet($this->applicantRepository->summary());
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function show(int $id): array
    {
        try {
            $applicant = $this->applicantRepository->find($id);

            if (! $applicant) {
                return QueryResultHelper::onCustomError('Applicant not found');
            }

            return QueryResultHelper::onSuccessGet($applicant);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    public function document(int $id): array
    {
        try {
            $document = $this->applicantRepository->findDocument($id);

            if (! $document) {
                return QueryResultHelper::onCustomError('Document not found');
            }

            return QueryResultHelper::onSuccessGet($document);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // Admin manual entry (walk-in)
    public function storeManual(array $data, array $files, ?int $userId): array
    {
        try {
            $data['channel'] = 'walk_in';
            $data['created_by'] = $userId;

            return QueryResultHelper::onSuccessPost($this->applicantRepository->create($data, $files));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // // Public apply
    // public function store(array $data, array $files): array
    // {
    //     try {
    //         $data['name'] = $data['full_name'];
    //         unset($data['full_name']);

    //         return QueryResultHelper::onSuccessPost($this->applicantRepository->create($data, $files));
    //     } catch (Exception $e) {
    //         return QueryResultHelper::onError($e);
    //     }
    // }

    // Public apply
    public function store(array $data, array $files): array
    {
        try {
            $data['name'] = $data['full_name'];
            unset($data['full_name']);

            $applicant = $this->applicantRepository->create($data, $files);

            $this->sendConfirmation($applicant);

            return QueryResultHelper::onSuccessPost($applicant);
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // Hindi dapat mag-fail ang application kung pumalya ang email
    private function sendConfirmation(Applicant $applicant): void
    {
        try {
            Mail::to($applicant->email)->send(new ApplicationReceived($applicant));
        } catch (Throwable $e) {
            Log::warning('Application email failed', [
                'reference' => $applicant->reference,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    // PUT|PATCH: status at remarks
    public function update(int $id, array $data): array
    {
        try {
            $applicant = $this->applicantRepository->find($id);

            if (! $applicant) {
                return QueryResultHelper::onCustomError('Applicant not found');
            }

            if (isset($data['status']) && ($message = $this->blocked($applicant, $data['status']))) {
                return QueryResultHelper::onCustomError($message);
            }

            return QueryResultHelper::onSuccessPut($this->applicantRepository->update($applicant, $data));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // Hindi gumagalaw ang status. Mananatili ang employer kung kasama ito sa bagong job.
    public function assignJob(int $id, int $jobId): array
    {
        try {
            $applicant = $this->applicantRepository->find($id);

            if (! $applicant) {
                return QueryResultHelper::onCustomError('Applicant not found');
            }

            $job = $this->jobRepository->find($jobId);

            if (! $job || $job->status !== 'Published') {
                return QueryResultHelper::onCustomError('Only published job openings can be assigned.');
            }

            $keepEmployer = $applicant->employer_id && $job->employers->contains('id', $applicant->employer_id);

            $changes = [
                'job_opening_id' => $job->id,
                'employer_id'    => $keepEmployer ? $applicant->employer_id : null,
            ];

            // Interview pataas na pero mawawalan ng employer -> balik sa Shortlisted
            if (! $keepEmployer && in_array($applicant->status, Applicant::STATUSES_REQUIRING_EMPLOYER, true)) {
                $changes['status'] = 'Shortlisted';
            }

            return QueryResultHelper::onSuccessPut($this->applicantRepository->update($applicant, $changes));
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // Ang employer ay dapat isa sa mga employer ng job ng applicant
    public function assignEmployer(int $id, int $employerId): array
    {
        try {
            $applicant = $this->applicantRepository->find($id);

            if (! $applicant) {
                return QueryResultHelper::onCustomError('Applicant not found');
            }

            if (! $applicant->job) {
                return QueryResultHelper::onCustomError('Assign a job first.');
            }

            if (! $applicant->job->employers->contains('id', $employerId)) {
                return QueryResultHelper::onCustomError('That employer is not part of this job opening.');
            }

            return QueryResultHelper::onSuccessPut(
                $this->applicantRepository->update($applicant, ['employer_id' => $employerId])
            );
        } catch (Exception $e) {
            return QueryResultHelper::onError($e);
        }
    }

    // Interview pataas ay kailangan ng employer
    private function blocked(Applicant $applicant, string $status): ?string
    {
        if (! in_array($status, Applicant::STATUSES_REQUIRING_EMPLOYER, true) || $applicant->employer_id) {
            return null;
        }

        $hint = $applicant->job_opening_id ? 'assign an employer first' : 'assign a job and an employer first';

        return "Cannot move to {$status}: {$hint}.";
    }
}
