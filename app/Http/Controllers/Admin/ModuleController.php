<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Module\ModuleStoreRequest;
use App\Http\Requests\Admin\Module\ModuleUpdateRequest;
use App\Http\Resources\Admin\CourseResource;
use App\Http\Resources\Admin\ModuleResource;
use App\Models\Course;
use App\Models\Module;
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
        return inertia('Admin/Course/Module/Edit', [
            'course' => new CourseResource($course),
            'module' => new ModuleResource($module),
        ]);
    }

    /**
     * @param ModuleStoreRequest $moduleStoreRequest
     * @param Course $course
     * @return RedirectResponse
     */
    public function store(ModuleStoreRequest $moduleStoreRequest, Course $course): RedirectResponse
    {
        $this->moduleService->store($course, $moduleStoreRequest->validated());

        return redirect()->route('admin.course.module.index', $course);
    }

    /**
     * @param ModuleUpdateRequest $moduleUpdateRequest
     * @param Course $course
     * @param Module $module
     * @return RedirectResponse
     */
    public function update(ModuleUpdateRequest $moduleUpdateRequest, Course $course, Module $module): RedirectResponse
    {
        $this->moduleService->update($course, $module, $moduleUpdateRequest->validated());

        return redirect()->route('admin.course.module.index', $course);
    }

    /**
     * @param Course $course
     * @param Module $module
     * @return RedirectResponse
     */
    public function destroy(Course $course, Module $module): RedirectResponse
    {
        $this->moduleService->delete($course, $module);

        return redirect()->route('admin.course.module.index', $course);
    }
}
