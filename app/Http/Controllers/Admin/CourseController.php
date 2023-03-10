<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Course\CourseIndexRequest;
use App\Models\Course;
use App\Http\Requests\Admin\Course\CourseStoreRequest;
use App\Http\Requests\Admin\Course\CourseUpdateRequest;
use App\Http\Resources\Admin\CourseResource;
use App\Models\Category;
use App\Models\Genre;
use App\Services\Admin\CourseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CourseController extends Controller
{
    /**
     * @var CourseService
     */
    protected CourseService $courseService;

    /**
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(CourseIndexRequest $request): Response
    {
        Authorize::abortIfNot('course_index');

        $dataQuery = new DataQuery($request);

        $courses = $this->courseService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Course/Index', [
            'courses' => CourseResource::collection($courses),
            'query' => $dataQuery->query(),
            'canCourseStore' => Authorize::any('course_store'),
            'canCourseEdit' => Authorize::any('course_update'),
            'canCourseDestroy' => Authorize::any('course_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('course_store');

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $courses = Course::select('id', 'name')->orderBy('name')->get();
        $genres = Genre::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Course/Create',  [
            'categories' => $categories,
            'courses' => $courses,
            'genres' => $genres
        ]);
    }

     /**
     * @param CourseStoreRequest $courseStoreRequest
     * @return RedirectResponse
     */
    public function store(CourseStoreRequest $courseStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('course_store');

        $course = $this->courseService->store($courseStoreRequest->validated());

        return redirect()->route('admin.Course.edit', $course);
    }

    /**
     * @param Course $course
     * @return Response
     */
    public function edit(Course $course): Response
    {
        Authorize::abortIfNot('course_update');

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $courses = Course::select('id', 'name')->orderBy('name')->get();
        $genres = Genre::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Course/Edit',  [
            'course' => new CourseResource($course),
            'categories' => $categories,
            'courses' => $courses,
            'genres' => $genres,
            'canCourseDestroy' => Authorize::any('course_destroy'),
        ]);
    }

     /**
     * @param Course $course
     * @return Response
     */
    public function show(Course $course): Response
    {
        Authorize::abortIfNot('course_show');

        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $courses = Course::select('id', 'name')->orderBy('name')->get();
        $genres = Genre::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Course/Show',  [
            'course' => new CourseResource($course),
            'categories' => $categories,
            'courses' => $courses,
            'genres' => $genres,
        ]);
    }

    /**
     * @param CourseUpdateRequest $courseUpdateRequest
     * @param Course $course
     * @return RedirectResponse
     */
    public function update(CourseUpdateRequest $courseUpdateRequest, Course $course): RedirectResponse
    {
        Authorize::abortIfNot('course_update');

        $course = $this->courseService->update($course, $courseUpdateRequest->validated());

        return redirect()->route('admin.course.index');
    }

    /**
     * @param Course $course
     * @return RedirectResponse
     */
    public function destroy(Course $course): RedirectResponse
    {
        Authorize::abortIfNot('course_destroy');

        $this->courseService->delete($course);

        return redirect()->route('admin.course.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('course_destroy');

        $request->validate(['ids' => 'required|array|in:' . Course::get()->pluck('id')->join(',')]);

        $this->courseService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.course.index');
    }
}
