<?php

namespace App\Helpers\Http;

trait DataQueryTrait
{
    /**
     * @var string
     */
    private string $defaultOrderBy = 'id';

    /**
     * @return integer
     */
    public function page(): int
    {
        return request()->get('page', 1);
    }

    /**
     * @param integer $defaultRowsPerPage
     * @return integer
     */
    public function rowsPerPage(int $defaultRowsPerPage = 10): int
    {
        return request()->get('rowsPerPage', $defaultRowsPerPage);
    }

    /**
     * @param string $defaultSort
     * @return string
     */
    public function sort(string $defaultSort = 'asc'): string
    {
        return request()->get('sort', $defaultSort);
    }

    /**
     * @param string $defaultOrderBy
     * @return string
     */
    public function orderBy(null|string $defaultOrderBy = null): string
    {
        $this->defaultOrderBy = $defaultOrderBy ?? $this->defaultOrderBy;

        return request()->get('orderBy', $this->defaultOrderBy);
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return request()->get('filters', []);
    }

    /**
     * @return array
     */
    public function dataQuery(): array
    {
        return [
            'orderBy' => $this->orderBy(),
            'sort' => $this->sort(),
            'filters' => $this->filters(),
            'rowsPerPage' => $this->rowsPerPage(),
            'page' => $this->page(),
        ];
    }
}
