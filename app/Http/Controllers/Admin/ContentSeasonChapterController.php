<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Chapter\ChapterStoreRequest;
use App\Http\Requests\Admin\Chapter\ChapterUpdateRequest;
use App\Models\Content;
use App\Models\Chapter;
use App\Models\Season;
use App\Services\Admin\ChapterService;
use Illuminate\Http\RedirectResponse;

class ContentSeasonChapterController extends Controller
{
    /**
     * @var ChapterService
     */
    protected ChapterService $chapterService;

    /**
     * @param ChapterService $chapterService
     */
    public function __construct(ChapterService $chapterService)
    {
        $this->chapterService = $chapterService;
    }

    /**
     * @param ChapterStoreRequest $chapterStoreRequest
     * @param Content $content
     * @return RedirectResponse
     */
    public function store(ChapterStoreRequest $chapterStoreRequest, Content $content, Season $season): RedirectResponse
    {
        $this->chapterService->storeForSeason($season, $chapterStoreRequest->validated());

        return redirect()->route('admin.content.season.index', $content);
    }

    /**
     * @param ChapterUpdateRequest $chapterUpdateRequest
     * @param Content $content
     * @param Season $season
     * @param Chapter $chapter
     * @return RedirectResponse
     */
    public function update(ChapterUpdateRequest $chapterUpdateRequest, Content $content,  Season $season, Chapter $chapter): RedirectResponse
    {
        $this->chapterService->updateForSeason($season, $chapter, $chapterUpdateRequest->validated());

        return redirect()->route('admin.content.season.index', $content);
    }

    /**
     * @param Content $content
     * @param Season $season
     * @param Chapter $chapter
     * @return RedirectResponse
     */
    public function destroy(Content $content, Season $season, Chapter $chapter): RedirectResponse
    {
        $this->chapterService->deleteForSeason($season, $chapter);

        return redirect()->route('admin.content.season.index', $content);
    }
}
