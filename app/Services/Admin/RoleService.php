<?php

namespace App\Services\Admin;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class RoleService
{
     /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Role[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        return Role::when(Arr::get($filters, 'name'), function (Builder $builder, $name) {
            return $builder->where('name', 'like', "%{$name}%");
        })->when($orderBy, function(Builder $query) use ($orderBy, $sort){
            return $query->orderBy($orderBy, $sort);
        })->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Role
     */
    public function store(array $requestData = []): Role
    {
        $role = Role::create($requestData);

        $role->permissions()->sync(Arr::get($requestData, 'permission_ids', []));

        return $role;
    }

    /**
     * @param Role $role
     * @param array $requestData
     * @return Role
     */
    public function update(Role $role, array $requestData = []): Role
    {
        $role->update($requestData);

        $role->permissions()->sync(Arr::get($requestData, 'permission_ids', []));

        return $role;
    }

    /**
     * @param Role $role
     * @return boolean|null
     */
    public function delete(Role $role): ?bool
    {
        $role->users()->detach();
        $role->permissions()->detach();

        return $role->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($role = Role::find($id)) {
                $this->delete($role);
            }
        }
    }
}
