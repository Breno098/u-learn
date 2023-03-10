<?php

namespace App\Helpers\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DataQuery
{
    /**
     * @var string
     */
    private string $defaultOrderBy = 'id';

    /**
     * @var int
     */
    private int $defaultRowsPerPage = 10;

    /**
     * @var string
     */
    private string $defaultSort = 'asc';

    /**
     * @var Request
     */
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return integer
     */
    public function page(): int
    {
        return $this->request->get('page', 1);
    }

    /**
     * @param null|integer $defaultRowsPerPage
     * @return integer
     */
    public function rowsPerPage(null|int $defaultRowsPerPage = null): int
    {
        $this->defaultRowsPerPage = $defaultRowsPerPage ?? $this->defaultRowsPerPage;

        return $this->request->get('rowsPerPage', $this->defaultRowsPerPage);
    }

    /**
     * @param null|string $defaultSort
     * @return string
     */
    public function sort(null|string $defaultSort = null): string
    {
        $this->defaultSort = $defaultSort ?? $this->defaultSort;

        return $this->request->get('sort', $this->defaultSort);
    }

    /**
     * @param null|string $defaultOrderBy
     * @return string
     */
    public function orderBy(null|string $defaultOrderBy = null): string
    {
        $this->defaultOrderBy = $defaultOrderBy ?? $this->defaultOrderBy;

        return $this->request->get('orderBy', $this->defaultOrderBy);
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return $this->request->get('filters', []);
    }

    /**
     * @param string $filter
     * @param mixed $default
     * @return mixed
     */
    public function filter(string $filter, mixed $default = null): mixed
    {
        $filters = $this->filters();

        return Arr::get($filters, $filter, $default);
    }

    /**
     * @return array
     */
    public function query(): array
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
