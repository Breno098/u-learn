<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Partner\PartnerIndexRequest;
use App\Models\Partner;
use App\Http\Requests\Admin\Partner\PartnerStoreRequest;
use App\Http\Requests\Admin\Partner\PartnerUpdateRequest;
use App\Http\Resources\Admin\PartnerResource;
use App\Services\Admin\PartnerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class PartnerController extends Controller
{
    /**
     * @var PartnerService
     */
    protected PartnerService $partnerService;

    /**
     * @param PartnerService $partnerService
     */
    public function __construct(PartnerService $partnerService)
    {
        $this->partnerService = $partnerService;
    }

    /**
     * @param PartnerIndexRequest $request
     * @return Response
     */
    public function index(PartnerIndexRequest $request): Response
    {
        Authorize::abortIfNot('partner_index');

        $dataQuery = new DataQuery($request);

        $partners = $this->partnerService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Partner/Index', [
            'partners' => PartnerResource::collection($partners),
            'query' => $dataQuery->query(),

            'canPartnerStore' => Authorize::any('partner_store'),
            'canPartnerEdit' => Authorize::any('partner_update'),
            'canPartnerDestroy' => Authorize::any('partner_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('partner_store');

        return inertia('Admin/Partner/Create');
    }

    /**
     * @param PartnerStoreRequest $partnerStoreRequest
     * @return RedirectResponse
     */
    public function store(PartnerStoreRequest $partnerStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('partner_store');

        $partner = $this->partnerService->store($partnerStoreRequest->validated());

        return redirect()->route('admin.partner.edit', $partner);
    }

    /**
     * @param Partner $partner
     * @return Response
     */
    public function edit(Partner $partner): Response
    {
        Authorize::abortIfNot('partner_update');

        return inertia('Admin/Partner/Edit', [
            'partner' => new PartnerResource($partner),
            'canPartnerDestroy' => Authorize::any('partner_destroy'),
        ]);
    }

    /**
     * @param Partner $partner
     * @return Response
     */
    public function show(Partner $partner): Response
    {
        Authorize::abortIfNot('partner_show');

        return inertia('Admin/Partner/Show', [
            'partner' => new PartnerResource($partner)
        ]);
    }

    /**
     * @param PartnerUpdateRequest $partnerUpdateRequest
     * @param Partner $partner
     * @return RedirectResponse
     */
    public function update(PartnerUpdateRequest $partnerUpdateRequest, Partner $partner): RedirectResponse
    {
        Authorize::abortIfNot('partner_update');

        $partner = $this->partnerService->update($partner, $partnerUpdateRequest->validated());

        return redirect()->route('admin.partner.index');
    }

    /**
     * @param Partner $partner
     * @return RedirectResponse
     */
    public function destroy(Partner $partner): RedirectResponse
    {
        Authorize::abortIfNot('partner_destroy');

        $this->partnerService->delete($partner);

        return redirect()->route('admin.partner.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('partner_destroy');

        $request->validate(['ids' => 'required|array|in:' . Partner::get()->pluck('id')->join(',')]);

        $this->partnerService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.partner.index');
    }
}
