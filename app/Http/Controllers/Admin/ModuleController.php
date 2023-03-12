<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Http\Requests\Admin\Content\Season\ContentSeasonStoreRequest;
use App\Http\Requests\Admin\Content\Season\ContentSeasonUpdateRequest;
use App\Http\Resources\Admin\Content\ContentResource;
use App\Http\Resources\Admin\CourseResource;
use App\Http\Resources\Admin\ModuleResource;
use App\Http\Resources\Admin\SeasonResource;
use App\Models\Course;
use App\Models\Module;
use App\Models\Season;
use App\Services\Admin\ModuleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ModuleController extends Controller
{
    /**
     * @var ModuleService
     */
    protected ModuleService $moduleService;

    /**
     * @param ModuleService $moduleService
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

     /**
     * @param Course $course
     * @return Response
     */
    public function index(Course $course): Response
    {
        return inertia('Admin/Course/Module/Index', [
            'course' => new CourseResource($course),
            'modules' => ModuleResource::collection($course->modules)
        ]);
    }

    /**
     * @param Course $course
     * @return Response
     */
    public function create(Course $course): Response
    {
        return inertia('Admin/Course/Module/Create', [
            'course' => new CourseResource($course),
        ]);
    }

    /**
     * @param Course $course
     * @return Response
     */
    public function edit(Course $course, Module $module): Response
    {
        return inertia('Admin/Content/Title/Season/Edit', [
            'course' => new CourseResource($course),
            'module' => new ModuleResource($module),
        ]);
    }

    /**
     * @param ContentSeasonStoreRequest $contentSeasonStoreRequest
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(ContentSeasonStoreRequest $contentSeasonStoreRequest, Course $course): RedirectResponse
    {
        $this->seasonService->store($content, $contentSeasonStoreRequest->validated());

        return redirect()->route('admin.content.season.index', $course);
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
