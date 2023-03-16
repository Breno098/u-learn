<?php

namespace App\Services\Admin;

use App\Models\Certificate;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CertificateService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Certificate[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
         /** @var Certificate|Builder */
        $query = Certificate::query()->select(['certificates.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if (in_array($orderBy, (new Certificate)->getFillable())) {
            $query->orderBy("certificates.{$orderBy}", $sort);
        }

        return $query->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Certificate
     */
    public function store(array $requestData = []): Certificate
    {
        $certificate = Certificate::create($requestData);

        return $certificate;
    }

    /**
     * @param Certificate $certificate
     * @param array $requestData
     * @return Certificate
     */
    public function update(Certificate $certificate, array $requestData = []): Certificate
    {
        $certificate->update($requestData);

        return $certificate;
    }

    /**
     * @param Certificate $certificate
     * @return boolean|null
     */
    public function delete(Certificate $certificate): ?bool
    {
        return $certificate->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($certificate = Certificate::find($id)) {
                $this->delete($certificate);
            }
        }
    }
}
