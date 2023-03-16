<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Certificate\CertificateIndexRequest;
use App\Http\Requests\Admin\Certificate\CertificateStoreRequest;
use App\Http\Requests\Admin\Certificate\CertificateUpdateRequest;
use App\Http\Resources\Admin\CertificateResource;
use App\Models\Certificate;
use App\Services\Admin\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CertificateController extends Controller
{
    /**
     * @var CertificateService
     */
    protected CertificateService $certificateService;

    /**
     * @param CertificateService $certificateService
     */
    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * @param CertificateIndexRequest $request
     * @return Response
     */
    public function index(CertificateIndexRequest $request): Response
    {
        Authorize::abortIfNot('certificate_index');

        $dataQuery = new DataQuery($request);

        $certificates = $this->certificateService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Certificate/Index', [
            'certificates' => CertificateResource::collection($certificates),
            'query' => $dataQuery->query(),

            'canCertificateStore' => Authorize::any('certificate_store'),
            'canCertificateEdit' => Authorize::any('certificate_update'),
            'canCertificateDestroy' => Authorize::any('certificate_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('certificate_store');

        return inertia('Admin/Certificate/Create');
    }

    /**
     * @param CertificateStoreRequest $certificateStoreRequest
     * @return RedirectResponse
     */
    public function store(CertificateStoreRequest $certificateStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('certificate_store');

        $certificate = $this->certificateService->store($certificateStoreRequest->validated());

        return redirect()->route('admin.certificate.edit', $certificate);
    }

    /**
     * @param Certificate $certificate
     * @return Response
     */
    public function edit(Certificate $certificate): Response
    {
        Authorize::abortIfNot('certificate_update');

        return inertia('Admin/Certificate/Edit', [
            'certificate' => new CertificateResource($certificate),
            'canCertificateDestroy' => Authorize::any('certificate_destroy')
        ]);
    }

    /**
     * @param CertificateUpdateRequest $certificateUpdateRequest
     * @param Certificate $certificate
     * @return RedirectResponse
     */
    public function update(CertificateUpdateRequest $certificateUpdateRequest, Certificate $certificate): RedirectResponse
    {
        Authorize::abortIfNot('certificate_update');

        $this->certificateService->update($certificate, $certificateUpdateRequest->validated());

        return redirect()->route('admin.certificate.index');
    }

    /**
     * @param Certificate $certificate
     * @return RedirectResponse
     */
    public function destroy(Certificate $certificate): RedirectResponse
    {
        Authorize::abortIfNot('certificate_destroy');

        $this->certificateService->delete($certificate);

        return redirect()->route('admin.certificate.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('certificate_destroy');

        $request->validate(['ids' => 'required|array|in:' . Certificate::get()->pluck('id')->join(',')]);

        $this->certificateService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.certificate.index');
    }
}
