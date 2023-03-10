<?php

namespace App\Services\Admin;

use App\Models\Question;
use App\Models\Quizz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class QuizzService
{
    /**
     * @var QuestionService
     */
    protected QuestionService $questionService;

    /**
     * @param QuestionService $questionService
     */
    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Quizz[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var Quizz|Builder */
        $query  = Quizz::query()->select(['quizzes.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($orderBy === 'content_name') {
            $query->join('contents', 'quizzes.content_id', '=', 'contents.id')->orderBy('contents.name', $sort);
        } else if (in_array($orderBy, (new Quizz)->getFillable())) {
            $query->orderBy("quizzes.{$orderBy}", $sort);
        }

        return $rowsPerPage ? $query->paginate($rowsPerPage) : $query->get();
    }

    /**
     * @param array $requestData
     * @return Quizz
     */
    public function store(array $requestData = []): Quizz
    {
        /** @var Quizz */
        $quizz = Quizz::create($this->transform($requestData));

        $quizz->setLinkableTypeParse(Arr::get($requestData, 'linkable_type', 'content'));

        $this->uploadAnswerFile($quizz, Arr::get($requestData, 'answer_file'));

        $this->questionService->createOrUpdateQuestions($quizz, Arr::get($requestData, 'questions', []));

        return $quizz;
    }

   /**
     * @param Quizz $quizz
     * @param array $requestData
     * @return Quizz
     */
    public function update(Quizz $quizz, array $requestData = []): Quizz
    {
        $quizz->update($this->transform($requestData));

        $quizz->setLinkableTypeParse(Arr::get($requestData, 'linkable_type', 'content'));

        $this->uploadAnswerFile($quizz, Arr::get($requestData, 'answer_file'));

        $this->questionService->createOrUpdateQuestions($quizz, Arr::get($requestData, 'questions', []));

        return $quizz;
    }

    /**
     * @param Quizz $quizz
     * @return boolean|null
     */
    public function delete(Quizz $quizz): ?bool
    {
        $this->deleteAnswerFile($quizz);

        $quizz->questions()->each(fn(Question $question) => $this->questionService->delete($question));

        return $quizz->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($quizz = Quizz::find($id)) {
                $this->delete($quizz);
            }
        }
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transform(array $requestData): array
    {
        return [
            'name' => Arr::get($requestData, 'name'),
            'description' => Arr::get($requestData, 'description'),
            'linkable_id' => Arr::get($requestData, 'linkable_id'),
            // 'linkable_type' => Arr::get($requestData, 'linkable_type'),
            'content_id' => Arr::get($requestData, 'content_id'),
        ];
    }

    /**
     * @param Season $season
     * @return void
     */
    public function deleteAnswerFile(Quizz $quizz): void
    {
        if ($quizz->answer_file) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $quizz->answer_file));

            $quizz->update(['answer_file' => null]);
        }
    }

    /**
     * @param Season $season
     * @param UploadedFile $answerFile
     * @return void
     */
    public function updateAnswerFile(Quizz $quizz, UploadedFile $answerFile): void
    {
        $this->deleteAnswerFile($quizz);

        $quizz->update(['answer_file' => Storage::url(Storage::disk('public')->put('quizzes', $answerFile))]);
    }

    /**
     * @param Season $season
     * @param null|string|UploadedFile $answerFile
     * @return void
     */
    public function uploadAnswerFile(Quizz $quizz, null|string|UploadedFile $answerFile): void
    {
        if (! $answerFile) {
            $this->deleteAnswerFile($quizz);
        } else if ($answerFile instanceof UploadedFile) {
            $this->updateAnswerFile($quizz, $answerFile);
        }
    }
}
