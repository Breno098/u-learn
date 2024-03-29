<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lesson\LessonStoreRequest;
use App\Http\Requests\Admin\Lesson\LessonUpdateRequest;
use App\Http\Resources\Admin\CourseResource;
use App\Http\Resources\Admin\LessonResource;
use App\Http\Resources\Admin\ModuleResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Services\Admin\LessonService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class LessonController extends Controller
{
    /**
     * @var LessonService
     */
    protected LessonService $lessonService;

    /**
     * @param LessonService $lessonService
     */
    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    /**
     * @param Module $module
     * @return Response
     */
    public function create(Module $module): Response
    {
        return inertia('Admin/Course/Module/Lesson/Create', [
            'course' => new CourseResource($module->course),
            'module' => new ModuleResource($module),
        ]);
    }

    /**
     * @param Module $module
     * @param Lesson $lesson
     * @return Response
     */
    public function edit(Module $module, Lesson $lesson): Response
    {
        return inertia('Admin/Course/Module/Lesson/Edit', [
            'course' => new CourseResource($module->course),
            'module' => new ModuleResource($module),
            'lesson' => new LessonResource($lesson)
        ]);
    }

    /**
     * @param LessonStoreRequest $lessonStoreRequest
     * @param Module $module
     * @return RedirectResponse
     */
    public function store(LessonStoreRequest $lessonStoreRequest, Module $module): RedirectResponse
    {
        $this->lessonService->store($module, $lessonStoreRequest->validated());

        return redirect()->route('admin.course.module.index', $module->course);
    }

    /**
     * @param LessonUpdateRequest $lessonUpdateRequest
     * @param Module $module
     * @param Lesson $lesson
     * @return RedirectResponse
     */
    public function update(LessonUpdateRequest $lessonUpdateRequest, Module $module, Lesson $lesson): RedirectResponse
    {
        $this->lessonService->update($module, $lesson, $lessonUpdateRequest->validated());

        return redirect()->route('admin.course.module.index', $module->course);
    }

    /**
     * @param Course $course
     * @param Module $module
     * @param Lesson $lesson
     * @return RedirectResponse
     */
    public function destroy(Module $module, Lesson $lesson): RedirectResponse
    {
        $deleted = $this->lessonService->delete($lesson);

        if ($deleted) {
            $this->lessonService->reorder($module, $module->lessons->toArray());
        }

        return redirect()->route('admin.course.module.index', $module->course);
    }
}
