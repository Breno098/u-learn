<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ModuleService
{
     /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Student[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
         /** @var Module|Builder */
        $query = Module::query()->select(['modules.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if (in_array($orderBy, (new Module)->getFillable())) {
            $query->orderBy("modules.{$orderBy}", $sort);
        }

        return $query->paginate($rowsPerPage);
    }

    /**
     * @param array $requestData
     * @return Module
     */
    public function store(array $requestData = []): Module
    {
        $module = Module::create($this->transform($requestData));

        $this->uploadImage($module, Arr::get($requestData, 'image'));

        return $module;
    }

     /**
     * @param Module $module
     * @param array $requestData
     * @return Module
     */
    public function update(Module $module, array $requestData = []): Module
    {
        $module->update($this->transform($requestData));

        $this->uploadImage($module, Arr::get($requestData, 'image'));

        return $module;
    }

    /**
     * @param Module $module
     * @return bool|null
     */
    public function delete(Module $module): bool|null
    {
        /** @var LessonService $lessonService */
        $lessonService= app(LessonService::class);

        $module->lessons()->get()->map(fn(Lesson $lesson) => $lessonService->delete($lesson));
        /** @todo Adicionar serviço de exams */$module->exams()->get()->map(fn(Exam $exam) => $exam->delete());

        $this->deleteImage($module);

        return $module->delete();
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

    // /**
    //  * @param Course $course
    //  * @param array $newPositions
    //  * @return void
    //  */
    // public function reorder(Course $course, array $newPositions = []): void
    // {
    //     foreach ($newPositions as $key => $dataPosition) {
    //         if ($id = Arr::get($dataPosition,'id')) {
    //             /** @var Module */
    //             $module = $course->modules()->find($id);
    //             $module->update(['number' => $key + 1]);
    //         }
    //     }
    // }
}
