<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ExamService
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
     * @param array $requestData
     * @return array
     */
    public function transformData(array $requestData = []): array
    {
        return [
            'name' => Arr::get($requestData, 'name'),
            'description' => Arr::get($requestData, 'description'),
        ];
    }

    /**
     * @param Lesson $lesson
     * @param null|string|UploadedFile $wallpaper
     * @return void
     */
    public function uploadWallPaper(Lesson $lesson, null|string|UploadedFile $wallpaper): void
    {
        if (! $wallpaper) {
            $this->deleteWallpaper($lesson);
        } else if ($wallpaper instanceof UploadedFile) {
            $this->updateWallpaper($lesson, $wallpaper);
        }
    }

    /**
     * @param Lesson $lesson
     * @return void
     */
    public function deleteWallpaper(Lesson $lesson): void
    {
        if ($lesson->wallpaper) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $lesson->wallpaper));

            $lesson->update(['wallpaper' => null]);
        }
    }

    /**
     * @param Lesson $lesson
     * @param UploadedFile $wallpaper
     * @return void
     */
    public function updateWallpaper(Lesson $lesson, UploadedFile $wallpaper): void
    {
        $this->deleteWallpaper($lesson);

        $lesson->update([
            'wallpaper' => Storage::url(Storage::disk('public')->put('lessons', $wallpaper))
        ]);
    }

    /**
     * @param Module $module
     * @param array $requestData
     * @return Exam
     */
    public function store(Module $module, array $requestData = []): Exam
    {
        if (! Arr::get($requestData, 'number')) {
            Arr::set($requestData, 'number', $module && $module->all_items->isNotEmpty() ? $module->all_items->last()->number + 1 : 1);
        }

        /** @var Exam|null */
        $exam = $module->exams()->create($this->transformData($requestData, $module));

        $this->questionService->createOrUpdateQuestions($exam, Arr::get($requestData, 'questions', []));

        // $this->uploadWallPaper($lesson, Arr::get($requestData, 'wallpaper'));

        return $exam;
    }

    /**
     * @param Module $module
     * @param Exam $exam
     * @param array $requestData
     * @return Exam
     */
    public function update(Module $module, Exam $exam, array $requestData = []): Exam
    {
        /** @var Exam|null */
        $exam = $module->exams()->find($exam->id);

        if ($exam) {
            $exam->update($this->transformData($requestData));

            // $this->uploadWallPaper($exam, Arr::get($requestData, 'wallpaper'));

            $this->questionService->createOrUpdateQuestions($exam, Arr::get($requestData, 'questions', []));
        }

        return $exam;
    }

    /**
     * @param Lesson $lesson
     * @return boolean|null
     */
    public function delete(Lesson $lesson): ?bool
    {
        $this->deleteWallpaper($lesson);

        return $lesson->delete();
    }

    /**
     * @param Module $module
     * @param array $newPositions
     * @return void
     */
    public function reorder(Module $module, array $newPositions = []): void
    {
        foreach ($newPositions as $key => $dataPosition) {
            if ($id = Arr::get($dataPosition,'id')) {
                /** @var Lesson */
                $lesson = $module->lessons()->find($id);
                $lesson->update(['number' => $key + 1]);
            }
        }
    }
}
