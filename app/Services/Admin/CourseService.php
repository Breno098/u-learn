<?php

namespace App\Services\Admin;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    /**
     * @param array $filters
     * @param null|integer $rowsPerPage
     * @param null|string $orderBy
     * @param null|string $sort
     * @return Course[]|Collection|LengthAwarePaginator
     */
    public function index(
        array $filters = [],
        null|int $rowsPerPage = 10,
        null|string $orderBy = 'id',
        null|string $sort = 'asc'
    ): LengthAwarePaginator|Collection
    {
        /** @var Course|Builder */
        $query = Course::query()->select(['courses.*']);

        if ($name = Arr::get($filters, 'name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($orderBy === 'category_name') {
            $query->join('categories', 'courses.category_id', '=', 'categories.id')->orderBy('categories.name', $sort);
        } else if ($orderBy === 'sections_name') {
            $query->join('Course_section', 'courses.id', '=', 'Course_section.Course_id')
                ->join('sections', 'sections.id', '=', 'Course_section.section_id')
                ->orderBy('sections.name', $sort);
        } else if (in_array($orderBy, (new Course)->getFillable())) {
            $query->orderBy("courses.{$orderBy}", $sort);
        }

        return $rowsPerPage ? $query->paginate($rowsPerPage) : $query->get();
    }

    /**
     * @param array $requestData
     * @return Course
     */
    public function store(array $requestData = []): Course
    {
        $course = Course::create($this->transformData($requestData));

        $course->genres()->sync(Arr::get($requestData, 'genres'));

        $this->uploadImage($course, 'wallpaper_image', Arr::get($requestData, 'wallpaper_image'));
        $this->uploadImage($course, 'tumb_image', Arr::get($requestData, 'tumb_image'));

        return $course;
    }

   /**
     * @param Course $course
     * @param array $requestData
     * @return Course
     */
    public function update(Course $course, array $requestData = []): Course
    {
        if ($course->number_of_seasons < $course->seasons->count()) {
            throw new \Exception("Limite de temporadas", 1);
        }

        $course->update($this->transformData($requestData));

        $course->genres()->sync(Arr::get($requestData, 'genres'));

        $this->uploadImage($course, 'wallpaper_image', Arr::get($requestData, 'wallpaper_image'));
        $this->uploadImage($course, 'tumb_image', Arr::get($requestData, 'tumb_image'));

        return $course;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transformData(array $requestData): array
    {
        return [
            'name' => Arr::get($requestData, 'name'),
            'descritiption' => Arr::get($requestData, 'descritiption'),
            'level' => Arr::get($requestData, 'level'),
            'duration' => Arr::get($requestData, 'duration'),
            'video' => Arr::get($requestData, 'video'),
            'category_id' => Arr::get($requestData, 'category_id'),
            'certificate_id' => Arr::get($requestData, 'certificate_id'),
        ];
    }

    /**
     * @param Course $course
     * @return boolean|null
     */
    public function delete(Course $course): ?bool
    {
        $course->genres()->detach();

        $this->deleteImage($course, 'wallpaper_image');
        $this->deleteImage($course, 'tumb_image');

        return $course->delete();
    }

    /**
     * @param array $ids
     * @return void
     */
    public function deleteMultiple(array $ids = []): void
    {
        foreach ($ids as $id) {
            if ($course = Course::find($id)) {
                $this->delete($course);
            }
        }
    }

    /**
     * @param Course $course
     * @param string $fieldName
     * @return void
     */
    public function deleteImage(Course $course, string $fieldName): void
    {
        if ($course->$fieldName) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $course->$fieldName));
            $course->update([$fieldName => null]);
        }
    }

    /**
     * @param Course $course
     * @param string $fieldName
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Course $course, string $fieldName, UploadedFile $image): void
    {
        $this->deleteImage($course, $fieldName);

        $course->update([
            $fieldName => Storage::url(Storage::disk('public')->put('courses', $image))
        ]);
    }

    /**
     * @param Course $course
     * @param string $fieldName
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Course $course, string $fieldName, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($course, $fieldName);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($course, $fieldName, $image);
        }
    }
}
