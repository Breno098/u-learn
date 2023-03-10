<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ContentExport;
use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\ContentIndexRequest;
use App\Models\Content;
use App\Http\Requests\Admin\Content\ContentStoreRequest;
use App\Http\Requests\Admin\Content\ContentUpdateRequest;
use App\Http\Resources\Admin\Content\ContentResource;
use App\Http\Resources\Admin\Content\ContentSummaryResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Section;
use App\Services\Admin\ContentService;
use App\Services\Admin\TopContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ContentController extends Controller
{
    /**
     * @var ContentService
     */
    protected ContentService $contentService;

    /**
     * @var TopContentService
     */
    protected TopContentService $topContentService;

    /**
     * @param ContentService $contentService
     * @param TopContentService $topContentService
     */
    public function __construct(ContentService $contentService, TopContentService $topContentService)
    {
        $this->contentService = $contentService;
        $this->topContentService = $topContentService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(ContentIndexRequest $request): Response
    {
        Authorize::abortIfNot('content_index');

        $dataQuery = new DataQuery($request);

        $contents = $this->contentService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        $contentReleasedThisMonth = $this->contentService->launchesOfThisMonth();
        $contentsMostViewed = $this->contentService->mostViewed();
        $contentsExpiresIn60Days = $this->contentService->expiresIn60Days();

        return inertia('Admin/Content/Index', [
            'contents' => ContentResource::collection($contents),
            'query' => $dataQuery->query(),
            'contentReleasedThisMonth' => ContentSummaryResource::collection($contentReleasedThisMonth),
            'contentsMostViewed' => ContentSummaryResource::collection($contentsMostViewed),
            'contentsExpiresIn60Days' => ContentSummaryResource::collection($contentsExpiresIn60Days),
            'canContentStore' => Authorize::any('content_store'),
            'canContentEdit' => Authorize::any('content_update'),
            'canContentDestroy' => Authorize::any('content_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('content_store');

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $sections = Section::select('id', 'name')->orderBy('name')->get();
        $contents = Content::select('id', 'name')->orderBy('name')->get();
        $genres = Genre::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Content/Create',  [
            'categories' => $categories,
            'sections' => $sections,
            'contents' => $contents,
            'genres' => $genres
        ]);
    }

     /**
     * @param ContentStoreRequest $contentStoreRequest
     * @return RedirectResponse
     */
    public function store(ContentStoreRequest $contentStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('content_store');

        $content = $this->contentService->store($contentStoreRequest->validated());

        return redirect()->route('admin.content.edit', $content);
    }

    /**
     * @param Content $content
     * @return Response
     */
    public function edit(Content $content): Response
    {
        Authorize::abortIfNot('content_update');

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $sections = Section::select('id', 'name')->orderBy('name')->get();
        $contents = Content::select('id', 'name')->orderBy('name')->get();
        $genres = Genre::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Content/Edit',  [
            'content' => new ContentResource($content),
            'categories' => $categories,
            'sections' => $sections,
            'contents' => $contents,
            'genres' => $genres,
            'canContentDestroy' => Authorize::any('content_destroy'),
        ]);
    }

     /**
     * @param Content $content
     * @return Response
     */
    public function show(Content $content): Response
    {
        Authorize::abortIfNot('content_show');

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $sections = Section::select('id', 'name')->orderBy('name')->get();
        $contents = Content::select('id', 'name')->orderBy('name')->get();
        $genres = Genre::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Content/Show',  [
            'content' => new ContentResource($content),
            'categories' => $categories,
            'sections' => $sections,
            'contents' => $contents,
            'genres' => $genres,
        ]);
    }

    /**
     * @param ContentUpdateRequest $contentUpdateRequest
     * @param Content $content
     * @return RedirectResponse
     */
    public function update(ContentUpdateRequest $contentUpdateRequest, Content $content): RedirectResponse
    {
        Authorize::abortIfNot('content_update');

        $content = $this->contentService->update($content, $contentUpdateRequest->validated());

        return redirect()->route('admin.content.index');
    }

    /**
     * @param Content $content
     * @return RedirectResponse
     */
    public function destroy(Content $content): RedirectResponse
    {
        Authorize::abortIfNot('content_destroy');

        $this->contentService->delete($content);

        return redirect()->route('admin.content.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('content_destroy');

        $request->validate(['ids' => 'required|array|in:' . Content::get()->pluck('id')->join(',')]);

        $this->contentService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.content.index');
    }

    /**
     * @todo
     * @return Response
     */
    public function top(): Response
    {
        $contents = $this->topContentService->getTopPosition(10);

        return inertia();
    }

    /**
     * @param Request $request
     * @param Content $content
     * @return RedirectResponse
     */
    public function setPosition(Request $request, Content $content): RedirectResponse
    {
        $request->validate(['position' => 'required']);

        $this->topContentService->setPosition($content, $request->get('position'));

        return redirect()->back();
    }

    /**
     * @param Content $content
     * @return RedirectResponse
     */
    public function removePosition(Content $content): RedirectResponse
    {
        $this->topContentService->removeTopPosition($content);

        return redirect()->back();
    }

    /**
     * @param Content $content
     * @return RedirectResponse
     */
    public function titles(Content $content): RedirectResponse
    {
        if($content->has_seasons) {
            return redirect()->route('admin.content.season.index', $content);
        }

        return redirect()->route('admin.content.chapter.index', $content);
    }

    /**
     * @param ContentIndexRequest $request
     * @return ContentExport
     */
    public function export(ContentIndexRequest $request): ContentExport
    {
        Authorize::abortIfNot('content_index');

        $dataQuery = new DataQuery($request);

        $contents = $this->contentService->index(
            $dataQuery->filters(),
            null,
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return new ContentExport($contents);
    }
}
