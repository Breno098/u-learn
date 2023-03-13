<?php

namespace App\Services\Admin;

use App\Models\Chapter;
use App\Models\Content;
use App\Models\Lesson;
use App\Models\Season;
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
            'number' => Arr::get($requestData, 'number'),
            'duration' => Arr::get($requestData, 'duration'),
            'cast' => Arr::get($requestData, 'cast'),
            'direction' => Arr::get($requestData, 'direction'),
            'main_player' => Arr::get($requestData, 'main_player'),
            'vimeo_link' => Arr::get($requestData, 'vimeo_link'),
            'vimeo_embed' => Arr::get($requestData, 'vimeo_embed'),
            'sambatech_link' => Arr::get($requestData, 'sambatech_link'),
            'sambatech_embed' => Arr::get($requestData, 'sambatech_embed'),
        ];
    }

    /**
     * @param Chapter $chapter
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Chapter $chapter, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($chapter);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($chapter, $image);
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
     * @param Chapter $chapter
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Chapter $chapter, UploadedFile $image): void
    {
        $this->deleteImage($chapter);

        $chapter->update([
            'image' => Storage::url(Storage::disk('public')->put('chapters', $image))
        ]);
    }

    /**
     * @param Content $content
     * @param array $requestData
     * @return Chapter
     */
    public function storeForContent(Content $content, array $requestData = []): Chapter
    {
        $chapter = $content->chapter()->create($this->transformData($requestData));

        $this->uploadImage($chapter, Arr::get($requestData, 'image'));

        return $chapter;
    }

    /**
     * @param Content $content
     * @param Chapter $chapter
     * @param array $requestData
     * @return Chapter
     */
    public function updateForContent(Content $content, array $requestData = []): Chapter
    {
        $content->chapter->update($this->transformData($requestData));

        $this->uploadImage($content->chapter, Arr::get($requestData, 'image'));

        return $content->chapter;
    }

    /**
     * @param Content $content
     * @param Chapter $chapter
     * @return boolean|null
     */
    public function deleteForContent(Content $content): ?bool
    {
        if ($content->chapter) {
            $this->deleteImage($content->chapter);
            return $content->chapter->delete();
        }

        return false;
    }

    /**
     * @param Season $season
     * @param array $requestData
     * @return Chapter
     */
    public function storeForSeason(Season $season, array $requestData = []): Chapter
    {
        /** @var Chapter */
        $chapter = $season->chapters()->create($this->transformData($requestData));

        $this->uploadImage($chapter, Arr::get($requestData, 'image'));

        return $chapter;
    }

     /**
     * @param Season $season
     * @param Chapter $chapter
     * @param array $requestData
     * @return Chapter
     */
    public function updateForSeason(Season $season, Chapter $chapter, array $requestData = []): Chapter
    {
        /** @var Chapter|null */
        $chapter = $season->chapters()->find($chapter->id);

        if ($chapter) {
            $chapter->update($this->transformData($requestData));
            $this->uploadImage($chapter, Arr::get($requestData, 'image'));
        }

        return $chapter;
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
