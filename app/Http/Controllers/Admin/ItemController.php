<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Item\ItemIndexRequest;
use App\Http\Requests\Admin\Item\ItemStoreRequest;
use App\Http\Requests\Admin\Item\ItemUpdateRequest;
use App\Http\Resources\Admin\ItemResource;
use App\Models\Campaign;
use App\Models\Item;
use App\Services\Admin\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ItemController extends Controller
{
    /**
     * @var ItemService
     */
    protected ItemService $itemService;

    /**
     * @param ItemService $itemService
     */
    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }


    /**
     * @param ItemIndexRequest $request
     * @return Response
     */
    public function index(ItemIndexRequest $request): Response
    {
        Authorize::abortIfNot('item_index');

        $dataQuery = new DataQuery($request);

        $items = $this->itemService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Item/Index', [
            'items' => ItemResource::collection($items),
            'query' => $dataQuery->query(),

            'canItemStore' => Authorize::any('item_store'),
            'canItemEdit' => Authorize::any('item_update'),
            'canItemDestroy' => Authorize::any('item_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('item_store');

        $campaigns = Campaign::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Item/Create', [
            'campaigns' => $campaigns
        ]);
    }

     /**
     * @param ItemStoreRequest $itemStoreRequest
     * @return RedirectResponse
     */
    public function store(ItemStoreRequest $itemStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('item_store');

        $item = $this->itemService->store($itemStoreRequest->validated());

        return redirect()->route('admin.item.edit', $item);
    }

     /**
     * @param Item $item
     * @return Response
     */
    public function show(Item $item): Response
    {
        Authorize::abortIfNot('item_show');

        $campaigns = Campaign::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Item/Show', [
            'item' => new ItemResource($item),
            'campaigns' => $campaigns
        ]);
    }

    /**
     * @param Item $item
     * @return Response
     */
    public function edit(Item $item): Response
    {
        Authorize::abortIfNot('item_update');

        $campaigns = Campaign::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Item/Edit', [
            'item' => new ItemResource($item),
            'campaigns' => $campaigns,
            'canItemDestroy' => Authorize::any('item_destroy'),
        ]);
    }

    /**
     * @param ItemUpdateRequest $itemUpdateRequest
     * @param Item $item
     * @return RedirectResponse
     */
    public function update(ItemUpdateRequest $itemUpdateRequest, Item $item): RedirectResponse
    {
        Authorize::abortIfNot('item_update');

        $item = $this->itemService->update($item, $itemUpdateRequest->validated());

        return redirect()->route('admin.item.index');
    }

    /**
     * @param Item $item
     * @return RedirectResponse
     */
    public function destroy(Item $item): RedirectResponse
    {
        Authorize::abortIfNot('item_destroy');

        $this->itemService->delete($item);

        return redirect()->route('admin.item.index');
    }

     /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('item_destroy');

        $request->validate(['ids' => 'nullable|array|in:' . Item::get()->pluck('id')->join(',')]);

        $this->itemService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.item.index');
    }
}
