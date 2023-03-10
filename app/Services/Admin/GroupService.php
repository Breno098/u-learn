<?php

namespace App\Services\Admin;

use App\Models\Group;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class GroupService
{
    /**
     * @param array $filters
     * @param integer $perPage
     * @return Group[]|Collection|LengthAwarePaginator
     */
    public function index(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Group::filterName(Arr::get($filters, 'name'))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param array $requestData
     * @return Group
     */
    public function store(array $requestData = []): Group
    {
        $group = Group::create($requestData);

        return $group;
    }

    /**
     * @param Group $group
     * @param array $requestData
     * @return Group
     */
    public function update(Group $group, array $requestData = []): Group
    {
        $group->update($requestData);

        return $group;
    }

    /**
     * @param Group $group
     * @return boolean|null
     */
    public function delete(Group $group): ?bool
    {
        return $group->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($group = Group::find($id)) {
                $this->delete($group);
            }
        }
    }
}
