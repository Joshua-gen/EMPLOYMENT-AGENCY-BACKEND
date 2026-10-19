<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use App\Http\Requests\Management\AssignApplicantEmployerRequest;
use App\Http\Requests\Management\AssignApplicantJobRequest;
use App\Http\Requests\Management\StoreApplicantRequest;
use App\Http\Requests\Management\UpdateApplicantRequest;
use App\Services\Management\ApplicantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
 use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ApplicantController extends Controller
{
    public function __construct(protected ApplicantService $applicantService)
    {
    }

    // GET /api/management/applicants?search=&status=&source=&channel=&unassigned=&job_id=&country=&page=&per_page=
    public function index(Request $request): JsonResponse
    {
        return $this->respond($this->applicantService->index(
            $request->only(['search', 'status', 'source', 'channel', 'unassigned', 'job_id', 'country', 'per_page'])
        ));
    }

    // GET /api/management/applicants/summary
    public function summary(): JsonResponse
    {
        return $this->respond($this->applicantService->summary());
    }

    // GET /api/management/applicants/{id}
    public function show(int $id): JsonResponse
    {
        return $this->respond($this->applicantService->show($id));
    }

    // POST /api/management/applicants  (multipart/form-data, walk-in / manual entry)
    public function store(StoreApplicantRequest $request): JsonResponse
    {
        $data = $request->safe()->except(['resume', 'valid_id', 'passport', 'other_requirements']);

        $files = array_filter([
            'resume'   => $request->file('resume'),
            'valid_id' => $request->file('valid_id'),
            'passport' => $request->file('passport'),
            'other'    => $request->file('other_requirements'),
        ]);

        return $this->respond($this->applicantService->storeManual($data, $files, $request->user()?->id), 201);
    }

    // PUT|PATCH /api/management/applicants/{id}
    public function update(UpdateApplicantRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->applicantService->update($id, $request->validated()));
    }

    // PATCH /api/management/applicants/{id}/assign-job
    public function assignJob(AssignApplicantJobRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->applicantService->assignJob($id, (int) $request->validated('job_opening_id')));
    }

    // PATCH /api/management/applicants/{id}/assign-employer
    public function assignEmployer(AssignApplicantEmployerRequest $request, int $id): JsonResponse
    {
        return $this->respond($this->applicantService->assignEmployer($id, (int) $request->validated('employer_id')));
    }



    // GET /api/applicant-documents/{document}  (signed URL, nasa labas ng auth)
    public function download(int $document): BinaryFileResponse|JsonResponse
    {
        $result = $this->applicantService->document($document);

        if (! $result['status']) {
            return response()->json($result, 404);
        }

        $file = $result['data'];
        $disk = Storage::disk('local');

        abort_unless($disk->exists($file->path), 404);

        // linisin ang kahit anong stray output bago mag-serve
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        return response()->file($disk->path($file->path), [
            'Content-Type'        => $disk->mimeType($file->path) ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . addslashes($file->original_name) . '"',
        ]);
    }

    private function respond(array $result, int $successCode = 200): JsonResponse
    {
        return response()->json($result, $result['status'] ? $successCode : 400);
    }
}
