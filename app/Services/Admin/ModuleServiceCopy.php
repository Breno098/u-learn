<?php

namespace App\Services\Admin;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ModuleServiceCopy
{
    /**
     * @param Course $course
     * @param array $requestData
     * @return Module
     */
    public function store(Course $course, array $requestData = []): Module
    {
        $module = $course->modules()->create($this->transform($requestData, $course));

        $this->uploadImage($module, Arr::get($requestData, 'image'));

        return $module;
    }

     /**
     * @param Course $course
     * @param Module $module
     * @param array $requestData
     * @return Module
     */
    public function update(Course $course, Module $module, array $requestData = []): Module
    {
        $course->modules()->find($module->id)->update($this->transform($requestData));

        $this->uploadImage($module, Arr::get($requestData, 'image'));

        return $module;
    }

    /**
     * @param Course $course
     * @param Module $module
     * @return bool|null
     */
    public function delete(Course $course, Module $module): bool|null
    {
        /** @var Module */
        $module = $course->modules()->find($module->id);

        if ($module) {
            /** @var LessonService $lessonService */
            $lessonService= app(LessonService::class);

            $module->lessons()->get()->map(fn(Lesson $lesson) => $lessonService->delete($lesson));

            $this->deleteImage($module);

            return $module->delete();
        }

        return false;
    }

    /**
     * @param array $requestData
     * @param null|Course $course
     * @return array
     */
    private function transform(array $requestData, null|Course $course = null): array
    {
        return [
            'name' => Arr::get($requestData, 'name'),
            'description' => Arr::get($requestData, 'description'),
            'number' => Arr::get($requestData, 'number', $course && $course->modules->isNotEmpty() ? $course->modules->last()->number + 1 : 1)
        ];
    }

    /**
     * @param Module $module
     * @return void
     */
    public function deleteImage(Module $module): void
    {
        if ($module->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $module->image));

            $module->update(['image' => null]);
        }
    }

    /**
     * @param Module $module
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Module $module, UploadedFile $image): void
    {
        $this->deleteImage($module);

        $module->update([
            'image' => Storage::url(Storage::disk('public')->put('modules', $image))
        ]);
    }

    /**
     * @param Module $module
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Module $module, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($module);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($module, $image);
        }
    }

    /**
     * @param Course $course
     * @param array $newPositions
     * @return void
     */
    public function reorder(Course $course, array $newPositions = []): void
    {
        foreach ($newPositions as $key => $dataPosition) {
            if ($id = Arr::get($dataPosition,'id')) {
                /** @var Module */
                $module = $course->modules()->find($id);
                $module->update(['number' => $key + 1]);
            }
        }
    }
}
