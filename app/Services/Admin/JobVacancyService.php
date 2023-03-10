<?php

namespace App\Services\Admin;

use App\Models\JobVacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class JobVacancyService
{
     /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return JobVacancy[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        return JobVacancy::when(Arr::get($filters, 'title'), function (Builder $builder, $title) {
                return $builder->where('title', 'like', "%{$title}%");
            })->when($orderBy, function(Builder $query) use ($orderBy, $sort){
                return $query->orderBy($orderBy, $sort);
            })
            ->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return JobVacancy
     */
    public function store(array $requestData = []): JobVacancy
    {
        $jobVacancy = JobVacancy::create($this->transformData($requestData));

        $jobVacancy->students()->sync(Arr::get($requestData, 'students'));

        $jobVacancy->groups()->sync(Arr::get($requestData, 'groups'));

        return $jobVacancy;
    }

    /**
     * @param JobVacancy $jobVacancy
     * @param array $requestData
     * @return JobVacancy
     */
    public function update(JobVacancy $jobVacancy, array $requestData = []): JobVacancy
    {
        $jobVacancy->update($this->transformData($requestData));

        $jobVacancy->students()->sync(Arr::get($requestData, 'students'));

        $jobVacancy->groups()->sync(Arr::get($requestData, 'groups'));

        return $jobVacancy;
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
     * @param JobVacancy $jobVacancy
     * @return boolean|null
     */
    public function delete(JobVacancy $jobVacancy): ?bool
    {
        $jobVacancy->students()->sync([]);
        $jobVacancy->groups()->sync([]);

        return $jobVacancy->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($jobVacancy = JobVacancy::find($id)) {
                $this->delete($jobVacancy);
            }
        }
    }
}
