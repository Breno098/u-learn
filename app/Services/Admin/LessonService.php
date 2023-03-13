<?php

namespace App\Services\Admin;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LessonService
{
    /**
     * @param array $requestData
     * @return array
     */
    public function transformData(array $requestData = []): array
    {
        return [
            'name' => Arr::get($requestData, 'name'),
            'description' => Arr::get($requestData, 'description'),
            'number' => Arr::get($requestData, 'number'),
            'duration' => Arr::get($requestData, 'duration'),
            'video' => Arr::get($requestData, 'video'),
            'wallpaper' => Arr::get($requestData, 'wallpaper'),
            'release_type' => Arr::get($requestData, 'release_type'),
            'can_comments' => Arr::get($requestData, 'can_comments'),
        ];
    }

    /**
     * @param Lesson $lesson
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Lesson $lesson, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($lesson);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($lesson, $image);
        }
    }

    /**
     * @param Lesson $lesson
     * @return void
     */
    public function deleteImage(Lesson $lesson): void
    {
        if ($lesson->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $lesson->image));

            $lesson->update(['image' => null]);
        }
    }

    /**
     * @param Lesson $lesson
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Lesson $lesson, UploadedFile $image): void
    {
        $this->deleteImage($lesson);

        $lesson->update([
            'image' => Storage::url(Storage::disk('public')->put('chapters', $image))
        ]);
    }

    /**
     * @param Module $module
     * @param array $requestData
     * @return Lesson
     */
    public function store(Module $module, array $requestData = []): Lesson
    {
        /** @var Lesson */
        $lesson = $module->lessons()->create($this->transformData($requestData));

        $this->uploadImage($lesson, Arr::get($requestData, 'image'));

        return $lesson;
    }


    public function update(Module $module, Lesson $lesson, array $requestData = []): Lesson
    {
        /** @var Lesson|null */
        $lesson = $module->lessons()->find($lesson->id);

        if ($lesson) {
            $lesson->update($this->transformData($requestData));

            $this->uploadImage($lesson, Arr::get($requestData, 'image'));
        }

        return $lesson;
    }

    /**
     * @param Lesson $lesson
     * @return boolean|null
     */
    public function delete(Lesson $lesson): ?bool
    {
        $this->deleteImage($lesson);

        return $lesson->delete();
    }
}
