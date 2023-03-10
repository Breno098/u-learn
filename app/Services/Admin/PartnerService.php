<?php

namespace App\Services\Admin;

use App\Models\Partner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class PartnerService
{
   /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Partner[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var Partner|Builder */
        $queryPartner = Partner::query();

        if ($name = Arr::get($filters, 'name')) {
            $queryPartner->where('name', 'like', "%{$name}%");
        }

        if ($email = Arr::get($filters, 'email')) {
            $queryPartner->where('email', $email);
        }

        if (in_array($orderBy, (new Partner)->getFillable())) {
            $queryPartner->orderBy($orderBy, $sort);
        }

        return $queryPartner->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Partner
     */
    public function store(array $requestData = []): Partner
    {
        $partner = Partner::create($this->transformData($requestData));

        $this->uploadImage($partner, Arr::get($requestData, 'image'));

        return $partner;
    }

   /**
     * @param Partner $partner
     * @param array $requestData
     * @return Partner
     */
    public function update(Partner $partner, array $requestData = []): Partner
    {
        $partner->update($this->transformData($requestData));

        $this->uploadImage($partner, Arr::get($requestData, 'image'));

        return $partner;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transformData(array $requestData): array
    {
        $startAt = Arr::get($requestData, 'start_at') ? Carbon::createFromFormat('d/m/Y', Arr::get($requestData, 'start_at')) : null;
        $endAt = Arr::get($requestData, 'end_at') ? Carbon::createFromFormat('d/m/Y', Arr::get($requestData, 'end_at')) : null;

        return [
            'name' => Arr::get($requestData, 'name'),
            'company_name'=> Arr::get($requestData, 'company_name'),
            'phone'=> Arr::get($requestData, 'phone'),
            'email'=> Arr::get($requestData, 'email'),
            'link'=> Arr::get($requestData, 'link'),
            'start_at'=> $startAt,
            'end_at'=> $endAt,
        ];
    }

    /**
     * @param Partner $partner
     * @return boolean|null
     */
    public function delete(Partner $partner): ?bool
    {
        return $partner->delete();
    }

     /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($partner = Partner::find($id)) {
                $this->delete($partner);
            }
        }
    }

    /**
     * @param Partner $partner
     * @param UploadedFile|null $image
     * @return bool
     */
    public function uploadImage(Partner $partner, ?UploadedFile $image): bool
    {
        if (! $image) {
            return false;
        }

        if ($partner->image) {
            Storage::disk('public')->delete($partner->image);
        }

        return $partner->update([
            'image' => Storage::url(Storage::disk('public')->put('partners', $image))
        ]);
    }
}
