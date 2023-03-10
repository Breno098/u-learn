<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ExtraPlayerEnum;
use App\Enums\ExtraTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Http\Requests\Admin\Content\Extra\ContentExtraStoreRequest;
use App\Http\Requests\Admin\Content\Extra\ContentExtraUpdateRequest;
use App\Http\Resources\Admin\Content\ContentResource;
use App\Http\Resources\Admin\Content\ExtraResource;
use App\Models\Extra;
use App\Services\Admin\ContentExtraService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContentExtraController extends Controller
{
     /**
     * @var ContentExtraService
     */
    protected ContentExtraService $contentExtraService;

    /**
     * @param ContentExtraService $contentExtraService
     */
    public function __construct(ContentExtraService $contentExtraService)
    {
        $this->contentExtraService = $contentExtraService;
    }

     /**
     * @param Content $content
     * @return Response
     */
    public function index(Content $content): Response
    {
        return inertia('Admin/Content/Extra/Index',  [
            'content' => new ContentResource($content),
            'extras' => fn() => ExtraResource::collection($content->extras),
            'types' => ExtraTypeEnum::toArray(),
            'players' => ExtraPlayerEnum::toArray(),
        ]);
    }

    /**
     * @param ContentExtraStoreRequest $contentExtraStoreRequest
     * @param Content $content
     * @return RedirectResponse
     */
    public function store(ContentExtraStoreRequest $contentExtraStoreRequest, Content $content): RedirectResponse
    {
        $this->contentExtraService->store($content, $contentExtraStoreRequest->validated());

        return redirect()->route('admin.content.extra.index', $content);
    }

    /**
     * @param ContentExtraUpdateRequest $contentExtraUpdateRequest
     * @param Content $content
     * @param Extra $extra
     * @return RedirectResponse
     */
    public function update(ContentExtraUpdateRequest $contentExtraUpdateRequest, Content $content, Extra $extra): RedirectResponse
    {
        $this->contentExtraService->update($content, $extra, $contentExtraUpdateRequest->validated());

        return redirect()->back();
    }

    /**
     * @param Content $content
     * @param Extra $extra
     * @return RedirectResponse
     */
    public function destroy(Content $content, Extra $extra): RedirectResponse
    {
        $this->contentExtraService->delete($content, $extra);

        return redirect()->route('admin.content.extra.index', $content);
    }
}
