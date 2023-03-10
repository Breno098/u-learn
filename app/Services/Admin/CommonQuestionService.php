<?php

namespace App\Services\Admin;

use App\Models\CommonQuestion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

class CommonQuestionService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return CommonQuestion[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        return CommonQuestion::when(Arr::get($filters, 'question_text'), function (Builder $builder, $question_text) {
            return $builder->where('question_text', 'like', "%{$question_text}%");
        })->when(Arr::get($filters, 'answer_text'), function (Builder $builder, $answer_text) {
            return $builder->where('answer_text', 'like', "%{$answer_text}%");
        })->when($orderBy, function(Builder $query) use ($orderBy, $sort){
            return $query->orderBy($orderBy, $sort);
        })
        ->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return CommonQuestion
     */
    public function store(array $requestData = []): CommonQuestion
    {
        $commonQuestion = CommonQuestion::create($requestData);

        return $commonQuestion;
    }

   /**
     * @param CommonQuestion $commonQuestion
     * @param array $requestData
     * @return CommonQuestion
     */
    public function update(CommonQuestion $commonQuestion, array $requestData = []): CommonQuestion
    {
        $commonQuestion->update($requestData);

        return $commonQuestion;
    }

    /**
     * @param CommonQuestion $commonQuestion
     * @return boolean|null
     */
    public function delete(CommonQuestion $commonQuestion): ?bool
    {
        return $commonQuestion->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($commonQuestion = CommonQuestion::find($id)) {
                $this->delete($commonQuestion);
            }
        }
    }
}
