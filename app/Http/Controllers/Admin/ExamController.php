<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AnswerTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lesson\LessonStoreRequest;
use App\Http\Requests\Admin\Lesson\LessonUpdateRequest;
use App\Http\Resources\Admin\CourseResource;
use App\Http\Resources\Admin\ExamResource;
use App\Http\Resources\Admin\ModuleResource;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Module;
use App\Services\Admin\ExamService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class ExamController extends Controller
{
    /**
     * @var examService
     */
    protected ExamService $examService;

    /**
     * @param ExamService $examService
     */
    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    /**
     * @param Module $module
     * @return Response
     */
    public function create(Module $module): Response
    {
        return inertia('Admin/Course/Module/Exam/Create', [
            'course' => new CourseResource($module->course),
            'module' => new ModuleResource($module),
        ]);
    }

    /**
     * @param Module $module
     * @param Exam $exam
     * @return Response
     */
    public function edit(Module $module, Exam $exam): Response
    {
        return inertia('Admin/Course/Module/Exam/Edit', [
            'course' => new CourseResource($module->course),
            'module' => new ModuleResource($module),
            'exam' => new ExamResource($exam),
            'answerTypes' => AnswerTypeEnum::toArray(),
        ]);
    }

    /**
     * @param LessonStoreRequest $lessonStoreRequest
     * @param Module $module
     * @return RedirectResponse
     */
    public function store(LessonStoreRequest $lessonStoreRequest, Module $module): RedirectResponse
    {
        $this->examService->store($module, $lessonStoreRequest->validated());

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
        $this->examService->update($module, $lesson, $lessonUpdateRequest->validated());

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
        $deleted = $this->examService->delete($lesson);

        if ($deleted) {
            $this->examService->reorder($module, $module->lessons->toArray());
        }

        return redirect()->route('admin.course.module.index', $module->course);
    }
}
