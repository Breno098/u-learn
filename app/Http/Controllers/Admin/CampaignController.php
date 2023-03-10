<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Campaign\CampaignIndexRequest;
use App\Models\Campaign;
use App\Http\Requests\Admin\Campaign\CampaignStoreRequest;
use App\Http\Requests\Admin\Campaign\CampaignUpdateRequest;
use App\Http\Resources\Admin\CampaignResource;
use App\Services\Admin\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CampaignController extends Controller
{
    /**
     * @var CampaignService
     */
    protected CampaignService $campaignService;

    /**
     * @param campaignService $campaignService
     */
    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * @param CampaignIndexRequest $request
     * @return Response
     */
    public function index(CampaignIndexRequest $request): Response
    {
        Authorize::abortIfNot('campaign_index');

        $dataQuery = new DataQuery($request);

        $campaigns = $this->campaignService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Campaign/Index', [
            'campaigns' => CampaignResource::collection($campaigns),
            'query' => $dataQuery->query(),

            'canCampaignStore' => Authorize::any('campaign_store'),
            'canCampaignEdit' => Authorize::any('campaign_update'),
            'canCampaignDestroy' => Authorize::any('campaign_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('campaign_store');

        return inertia('Admin/Campaign/Create');
    }

    /**
     * @param CampaignStoreRequest $campaignStoreRequest
     * @return RedirectResponse
     */
    public function store(CampaignStoreRequest $campaignStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('campaign_store');

        $campaign = $this->campaignService->store($campaignStoreRequest->validated());

        return redirect()->route('admin.campaign.edit', $campaign);
    }

    /**
     * @param Campaign $campaign
     * @return Response
     */
    public function edit(Campaign $campaign): Response
    {
        Authorize::abortIfNot('campaign_update');

        return inertia('Admin/Campaign/Edit', [
            'campaign' => new CampaignResource($campaign),
            'canCampaignDestroy' => Authorize::any('campaign_destroy'),
        ]);
    }

    /**
     * @param Campaign $campaign
     * @return Response
     */
    public function show(Campaign $campaign): Response
    {
        Authorize::abortIfNot('campaign_show');

        return inertia('Admin/Campaign/Show', [
            'campaign' => new CampaignResource($campaign)
        ]);
    }

    /**
     * @param CampaignUpdateRequest $campaignUpdateRequest
     * @param Campaign $campaign
     * @return RedirectResponse
     */
    public function update(CampaignUpdateRequest $campaignUpdateRequest, Campaign $campaign): RedirectResponse
    {
        Authorize::abortIfNot('campaign_update');

        $campaign = $this->campaignService->update($campaign, $campaignUpdateRequest->validated());

        return redirect()->route('admin.campaign.index');
    }

    /**
     * @param Campaign $campaign
     * @return RedirectResponse
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        Authorize::abortIfNot('campaign_destroy');

        $this->campaignService->delete($campaign);

        return redirect()->route('admin.campaign.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('campaign_destroy');

        $request->validate(['ids' => 'required|array|in:' . Campaign::get()->pluck('id')->join(',')]);

        $this->campaignService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.campaign.index');
    }
}
