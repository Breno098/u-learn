<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Http\Requests\Admin\Content\Season\ContentSeasonStoreRequest;
use App\Http\Requests\Admin\Content\Season\ContentSeasonUpdateRequest;
use App\Http\Resources\Admin\Content\ContentResource;
use App\Http\Resources\Admin\SeasonResource;
use App\Models\Season;
use App\Services\Admin\SeasonService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class SeasonController extends Controller
{
    /**
     * @var SeasonService
     */
    protected SeasonService $seasonService;

    /**
     * @param SeasonService $seasonService
     */
    public function __construct(SeasonService $seasonService)
    {
        $this->seasonService = $seasonService;
    }

     /**
     * @param Content $content
     * @return Response
     */
    public function index(Content $content): Response
    {
        return inertia('Admin/Content/Title/Season/Index', [
            'content' => new ContentResource($content),
            'seasons' => SeasonResource::collection($content->seasons()->orderBy('number')->get())
        ]);
    }

    /**
     * @param Content $content
     * @return Response
     */
    public function create(Content $content): Response
    {
        $numbers = [];

        foreach (range(1, $content->number_of_seasons) as $value) {
            $numbers[] = [
                'position' => $value,
                'empty' => $content->seasons()->where('number', $value)->doesntExist()
            ];
        }

        return inertia('Admin/Content/Title/Season/Create', [
            'content' => new ContentResource($content),
            'numbers' => $numbers
        ]);
    }

    /**
     * @param Content $content
     * @return Response
     */
    public function edit(Content $content, Season $season): Response
    {
        $numbers = [];

        foreach (range(1, $content->number_of_seasons) as $value) {
            $numbers[] = [
                'position' => $value,
                'empty' => $season->number == $value || $content->seasons()->where('number', $value)->doesntExist()
            ];
        }

        return inertia('Admin/Content/Title/Season/Edit', [
            'content' => new ContentResource($content),
            'season' => new SeasonResource($season),
            'numbers' => $numbers,
        ]);
    }

    /**
     * @param Content $content
     * @return Response
     */
    public function show(Content $content, Season $season): Response
    {
        return inertia('Admin/Content/Title/Season/Show', [
            'content' => new ContentResource($content),
            'season' => new SeasonResource($season),
        ]);
    }

    /**
     * @param ContentSeasonStoreRequest $contentSeasonStoreRequest
     * @param Content $content
     * @return RedirectResponse
     */
    public function store(ContentSeasonStoreRequest $contentSeasonStoreRequest, Content $content): RedirectResponse
    {
        $this->seasonService->store($content, $contentSeasonStoreRequest->validated());

        return redirect()->route('admin.content.season.index', $content);
    }

    /**
     * @param ContentSeasonUpdateRequest $contentSeasonUpdateRequest
     * @param Content $content
     * @param Season $season
     * @return RedirectResponse
     */
    public function update(ContentSeasonUpdateRequest $contentSeasonUpdateRequest, Content $content, Season $season): RedirectResponse
    {
        $this->seasonService->update($content, $season, $contentSeasonUpdateRequest->validated());

        return redirect()->route('admin.content.season.index', $content);
    }

    /**
     * @param Content $content
     * @param Season $season
     * @return RedirectResponse
     */
    public function destroy(Content $content, Season $season): RedirectResponse
    {
        $this->seasonService->delete($content, $season);

        return redirect()->route('admin.content.season.index', $content);
    }
}
