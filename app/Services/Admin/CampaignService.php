<?php

namespace App\Services\Admin;

use App\Models\Campaign;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CampaignService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Campaign[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        return Campaign::when(Arr::get($filters, 'name'), function (Builder $builder, $name) {
                return $builder->where('name', 'like', "%{$name}%");
            })->when(Arr::get($filters, 'start_at'), function (Builder $builder, $startAt) {
                return $builder->where('start_at', '>=', Carbon::createFromFormat('d/m/Y', $startAt));
            })->when(Arr::get($filters, 'end_at'), function (Builder $builder, $endAt) {
                return $builder->where('end_at', '<=', Carbon::createFromFormat('d/m/Y', $endAt));
            })->when($orderBy, function(Builder $query) use ($orderBy, $sort){
                return $query->orderBy($orderBy, $sort);
            })
            ->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Campaign
     */
    public function store(array $requestData = []): Campaign
    {
        $campaign = Campaign::create($this->transformData($requestData));

        return $campaign;
    }

   /**
     * @param Campaign $campaign
     * @param array $requestData
     * @return Campaign
     */
    public function update(Campaign $campaign, array $requestData = []): Campaign
    {
        $campaign->update($this->transformData($requestData));

        return $campaign;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transformData(array $requestData): array
    {
        $startAt = Arr::get($requestData, 'start_at') ? Carbon::createFromFormat('d/m/Y', Arr::get($requestData, 'start_at')) : null;
        $endAt = Arr::get($requestData, 'end_at') ? Carbon::createFromFormat('d/m/Y', Arr::get($requestData, 'end_at')) : null;

        return array_merge($requestData, [
            'start_at' => $startAt,
            'end_at' => $endAt
        ]);
    }

    /**
     * @param Campaign $campaign
     * @return boolean|null
     */
    public function delete(Campaign $campaign): ?bool
    {
        $campaign->items()->each(fn(Item $item) => $item->campaign()->dissociate($campaign)->save());
        $campaign->orders()->each(fn(Order $order) => $order->campaign()->dissociate($campaign)->save());

        return $campaign->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($campaign = Campaign::find($id)) {
                $this->delete($campaign);
            }
        }
    }
}
