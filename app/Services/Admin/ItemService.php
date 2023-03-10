<?php

namespace App\Services\Admin;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ItemService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Item[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var Item|Builder */
        $queryItem = Item::query()->select(['items.*']);

        if ($name = Arr::get($filters, 'name')) {
            $queryItem->where('name', 'like', "%{$name}%");
        }

        if ($orderBy === 'campaign_name') {
            $queryItem->join('campaigns', 'items.campaign_id', '=', 'campaigns.id')->orderBy('campaigns.name', $sort);
        } else if (in_array($orderBy, (new Item)->getFillable())) {
            $queryItem->orderBy("items.{$orderBy}", $sort);
        }

        return $queryItem->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Item
     */
    public function store(array $requestData = []): Item
    {
        $item = Item::create($this->transformData($requestData));

        $this->uploadImage($item, Arr::get($requestData, 'image'));

        return $item;
    }

   /**
     * @param Item $item
     * @param array $requestData
     * @return Item
     */
    public function update(Item $item, array $requestData = []): Item
    {
        $item->update($this->transformData($requestData));

        $this->uploadImage($item, Arr::get($requestData, 'image'));

        return $item;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transformData(array $requestData): array
    {
        return Arr::except($requestData, ['image']);
    }

    /**
     * @param Item $item
     * @return boolean|null
     */
    public function delete(Item $item): ?bool
    {
        $this->deleteImage($item);

        return $item->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($item = Item::find($id)) {
                $this->delete($item);
            }
        }
    }

    /**
     * @param Item $item
     * @return void
     */
    public function deleteImage(Item $item): void
    {
        if ($item->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $item->image));

            $item->update(['image' => null]);
        }
    }

    /**
     * @param Item $item
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Item $item, UploadedFile $image): void
    {
        $this->deleteImage($item);

        $item->update([
            'image' => Storage::url(Storage::disk('public')->put('items', $image))
        ]);
    }

    /**
     * @param Item $item
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Item $item, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($item);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($item, $image);
        }
    }
}
