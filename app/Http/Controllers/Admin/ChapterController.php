<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Chapter\ChapterStoreRequest;
use App\Http\Requests\Admin\Chapter\ChapterUpdateRequest;
use App\Http\Requests\Admin\Chapter\ChapterUploadImageRequest;
use App\Models\Content;
use App\Models\Chapter;
use App\Models\Season;
use App\Services\Admin\ChapterService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ChapterController extends Controller
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
     * @param Content $content
     * @param Season $season
     * @return View
     */
    public function index(Content $content, Season $season): View
    {
        return view('admin.content.season.chapter.index', [
            'content' => $content,
            'season' => $season
        ]);
    }

    /**
     * @param Content $content
     * @param Season $season
     * @return View
     */
    public function create(Content $content, Season $season): View
    {
        return view('admin.content.season.chapter.create', [
            'content' => $content,
            'season' => $season
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Content $content
     * @param Season $season
     * @param Chapter $chapter
     * @return View
     */
    public function edit(Content $content, Season $season, Chapter $chapter): View
    {
        return view('admin.content.season.chapter.edit', [
            'content' => $content,
            'season' => $season,
            'chapter' => $chapter
        ]);
    }

    /**
     * @param ChapterStoreRequest $chapterStoreRequest
     * @param Content $content
     * @param Season $season
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
    public function update(ChapterUpdateRequest $chapterUpdateRequest, Content $content, Season $season, Chapter $chapter): RedirectResponse
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

     /**
     * @param Content $content
     * @param Chapter $chapter
     * @param ChapterUploadImageRequest $chapterUploadImageRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadImage(Content $content, Chapter $chapter, ChapterUploadImageRequest $chapterUploadImageRequest): \Illuminate\Http\RedirectResponse
    {
        $this->chapterService->uploadImage($chapter, $chapterUploadImageRequest->file('image'));

        return redirect()->route('admin.content.images', $content);
    }

    /**
     * @param Content $content
     * @param ChapterStoreRequest $chapterStoreRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeForContent(Content $content, ChapterStoreRequest $chapterStoreRequest): \Illuminate\Http\RedirectResponse
    {
        $this->chapterService->storeForContent($content, $chapterStoreRequest->validated());

        return redirect()->route('admin.content.titles', $content);
    }

    /**
     * @param Content $content
     * @param Chapter $chapter
     * @param ChapterStoreRequest $contentUpdateRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateForContent(Content $content, Chapter $chapter, ChapterStoreRequest $contentUpdateRequest): \Illuminate\Http\RedirectResponse
    {
        $this->chapterService->updateForContent($content, $contentUpdateRequest->validated());

        return redirect()->route('admin.content.titles', $content);
    }
}
