<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CategoryService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Category[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        return Category::when(Arr::get($filters, 'name'), function (Builder $builder, $name) {
                return $builder->where('name', 'like', "%{$name}%");
            })->when($orderBy, function(Builder $query) use ($orderBy, $sort){
                return $query->orderBy($orderBy, $sort);
            })
            ->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Category
     */
    public function store(array $requestData = []): Category
    {
        $category = Category::create($requestData);

        return $category;
    }

    /**
     * @param Category $category
     * @param array $requestData
     * @return Category
     */
    public function update(Category $category, array $requestData = []): Category
    {
        $category->update($requestData);

        return $category;
    }

    /**
     * @param Category $category
     * @return boolean|null
     */
    public function delete(Category $category): ?bool
    {
        $category->courses->map(function(Course $course){
            $course->category()->dissociate()->save();
        });

        return $category->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($category = Category::find($id)) {
                $this->delete($category);
            }
        }
    }
}
