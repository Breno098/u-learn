<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Chapter\ChapterStoreRequest;
use App\Http\Requests\Admin\Chapter\ChapterUpdateRequest;
use App\Http\Resources\Admin\ChapterResource;
use App\Http\Resources\Admin\Content\ContentResource;
use App\Models\Content;
use App\Models\Chapter;
use App\Models\Season;
use App\Services\Admin\ChapterService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ContentChapterController extends Controller
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
     * @param Chapter $chapter
     * @return Response
     */
    public function index(Content $content): Response
    {
        if ($content->chapter) {
            return inertia('Admin/Content/Title/Chapter/Edit', [
                'content' => new ContentResource($content),
                'chapter' => new ChapterResource($content->chapter),
            ]);
        }

        return inertia('Admin/Content/Title/Chapter/Create', [
            'content' => new ContentResource($content),
        ]);
    }

    /**
     * @param ChapterStoreRequest $chapterStoreRequest
     * @param Content $content
     * @return RedirectResponse
     */
    public function store(ChapterStoreRequest $chapterStoreRequest, Content $content): RedirectResponse
    {
        $chapter = $this->chapterService->storeForContent($content, $chapterStoreRequest->validated());

        return redirect()->route('admin.content.chapter.index', $content);
    }

    /**
     * @param ChapterUpdateRequest $chapterUpdateRequest
     * @param Content $content
     * @param Season $season
     * @param Chapter $chapter
     * @return RedirectResponse
     */
    public function update(ChapterUpdateRequest $chapterUpdateRequest, Content $content, Chapter $chapter): RedirectResponse
    {
        $chapter = $this->chapterService->updateForContent($content, $chapter, $chapterUpdateRequest->validated());

        return redirect()->route('admin.content.chapter.index', $content);
    }

    /**
     * @param Content $content
     * @param Season $season
     * @param Chapter $chapter
     * @return RedirectResponse
     */
    public function destroy(Content $content, Chapter $chapter): RedirectResponse
    {
        $this->chapterService->deleteForContent($content, $chapter);

        return redirect()->route('admin.content.chapter.index', $content);
    }
}
