<?php

namespace App\Services\Admin;

use App\Models\Chapter;
use App\Models\Content;
use App\Models\Season;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ModuleService
{
    /**
     * @param Content $content
     * @param array $requestData
     * @return Season
     */
    public function store(Content $content, array $requestData = []): Season
    {
        $season = $content->seasons()->create($this->transform($requestData));

        $this->uploadImage($season, Arr::get($requestData, 'image'));

        return $season;
    }

     /**
     * @param Content $content
     * @param Season $season
     * @param array $requestData
     * @return Extra
     */
    public function update(Content $content, Season $season, array $requestData = []): Season
    {
        $content->seasons()->find($season->id)->update($this->transform($requestData));

        $this->uploadImage($season, Arr::get($requestData, 'image'));

        return $season;
    }

    /**
     * @param Content $content
     * @param Season $season
     * @return bool|null
     */
    public function delete(Content $content, Season $season): bool|null
    {
        $season = $content->seasons()->find($season->id);

        if ($season) {
            /** @var ChapterService $chapterService */
            $chapterService= app(ChapterService::class);

            $season->chapters->map(function(Chapter $chapter) use ($season, $chapterService) {
                $chapterService->deleteForSeason($season, $chapter);
            });

            $this->deleteImage($season);

            return $season->delete();
        }

        return false;
    }

    /**
     * @param array $requestData
     * @return array
     */
    private function transform(array $requestData): array
    {
        return [
            'name' => Arr::get($requestData, 'name'),
            'number' => Arr::get($requestData, 'number'),
            'number_of_chapters' => Arr::get($requestData, 'number_of_chapters'),
        ];
    }

    /**
     * @param Season $season
     * @return void
     */
    public function deleteImage(Season $season): void
    {
        if ($season->image) {
            Storage::disk('public')->delete(Str::replaceFirst('storage', '', $season->image));

            $season->update(['image' => null]);
        }
    }

    /**
     * @param Season $season
     * @param UploadedFile $image
     * @return void
     */
    public function updateImage(Season $season, UploadedFile $image): void
    {
        $this->deleteImage($season);

        $season->update([
            'image' => Storage::url(Storage::disk('public')->put('seasons', $image))
        ]);
    }

    /**
     * @param Season $season
     * @param null|string|UploadedFile $image
     * @return void
     */
    public function uploadImage(Season $season, null|string|UploadedFile $image): void
    {
        if (! $image) {
            $this->deleteImage($season);
        } else if ($image instanceof UploadedFile) {
            $this->updateImage($season, $image);
        }
    }
}
