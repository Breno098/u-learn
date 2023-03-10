<?php

namespace App\Services\Admin;

use App\Enums\AnswerTypeEnum;
use App\Models\Question;
use App\Models\Quizz;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionService
{
    /**
     * @param Quizz $quizz
     * @param null|array $requestQuestions
     * @return void
     */
    public function deleteRecordsOutsideTheIdRange(Quizz $quizz, null|array $requestQuestions = []): void
    {
        if (is_array($requestQuestions)) {
            $quizz->questions()
                ->whereNotIn('id', data_get($requestQuestions, '*.id'))
                ->each(fn(Question $question) => $question->delete());
        }
    }

    /**
     * @param Question $question
     * @return boolean
     */
    public function delete(Question $question): bool
    {
        $question->alternatives()->delete();

        return $question->delete();
    }

    /**
     * @param Quizz $quizz
     * @param null|array $requestQuestions
     * @return void
     */
    public function createOrUpdateQuestions(Quizz $quizz, null|array $requestQuestions = []): void
    {
        $this->deleteRecordsOutsideTheIdRange($quizz, $requestQuestions);

        if($requestQuestions) {
            $count = 1;

            foreach ($requestQuestions as $requestQuestion) {
                $hasVideo = Arr::get($requestQuestion, 'has_video');
                $hasAudio = Arr::get($requestQuestion, 'has_audio');
                $hasImage = Arr::get($requestQuestion, 'has_image');

                /** @var Question */
                $question = $quizz->questions()->updateOrCreate(
                    [
                        'id' =>  Arr::get($requestQuestion, 'id')
                    ],
                    [
                        'title' => Arr::get($requestQuestion, 'title'),
                        'answer_type' => Arr::get($requestQuestion, 'answer_type'),
                        'number' => $count++,
                        'has_video' => $hasVideo,
                        'video' => $hasVideo ? Arr::get($requestQuestion, 'video') : null,
                        'has_audio' => $hasAudio,
                        'audio' => $hasAudio ? Arr::get($requestQuestion, 'audio') : null,
                        'has_image' => $hasImage,
                    ]
                );

                $this->uploadImage($question, Arr::get($requestQuestion, 'image'));

                if (! $question->wasRecentlyCreated) {
                    $question->alternatives()->delete();
                }

                if ($question->answer_type === AnswerTypeEnum::fechada()->value) {
                    $this->createAlternatives($question, Arr::get($requestQuestion, 'alternatives'));
                }
            }
        }
    }

     /**
     * @param Question $question
     * @param array $alternatives
     * @return void
     */
    private function createAlternatives(Question $question, array $alternatives = []): void
    {
        $count = 1;

        foreach ($alternatives as $alternative) {
            if ($name = Arr::get($alternative, 'name')) {
                $question->alternatives()->create(['name' => $name, 'number' => $count++]);
            }
        }
    }

     /**
     * @param Season $season
     * @return void
     */
    public function deleteImage(Question $question): void
    {
        if ($question->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $question->image));

            $question->update(['image' => null]);
        }
    }

    /**
     * @param Question $question
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Question $question, UploadedFile $image): void
    {
        $this->deleteImage($question);

        $question->update(['image' => Storage::url(Storage::disk('public')->put('quizzes/questions', $image))]);
    }

    /**
     * @param Question $question
     * @param null|string|UploadedFile $answerFile
     * @return void
     */
    public function uploadImage(Question $question, null|string|UploadedFile $image): void
    {
        if (!$image || !$question->has_image) {
            $this->deleteImage($question);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($question, $image);
        }
    }
}
