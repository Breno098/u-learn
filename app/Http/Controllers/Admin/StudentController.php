<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Student\StudentIndexRequest;
use App\Models\Group;
use App\Models\Student;
use App\Http\Requests\Admin\Student\StudentStoreRequest;
use App\Http\Requests\Admin\Student\StudentUpdateRequest;
use App\Http\Resources\Admin\StudentResource;
use App\Services\Admin\StudentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class StudentController extends Controller
{
    /**
     * @var StudentService
     */
    protected StudentService $studentService;

    /**
     * @param StudentService $studentService
     */
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * @param StudentIndexRequest $request
     * @return Response
     */
    public function index(StudentIndexRequest $request): Response
    {
        Authorize::abortIfNot('student_index');

        $dataQuery = new DataQuery($request);

        $students = $this->studentService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Student/Index', [
            'students' => StudentResource::collection($students),
            'query' => $dataQuery->query(),

            'canStudentStore' => Authorize::any('student_store'),
            'canStudentEdit' => Authorize::any('student_update'),
            'canStudentDestroy' => Authorize::any('student_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('student_store');

        return inertia('Admin/Student/Create', [
            'groups' => Group::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * @param StudentStoreRequest $studentStoreRequest
     * @return RedirectResponse
     */
    public function store(StudentStoreRequest $studentStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('student_store');

        $student = $this->studentService->store($studentStoreRequest->validated());

        return redirect()->route('admin.student.edit', $student);
    }

    /**
     * @param Student $student
     * @return Response
     */
    public function edit(Student $student): Response
    {
        Authorize::abortIfNot('student_update');

        return inertia('Admin/Student/Edit', [
            'groups' => Group::select('id', 'name')->orderBy('name')->get(),
            'student' => new StudentResource($student),
            'canStudentDestroy' => Authorize::any('student_destroy')
        ]);
    }

    /**
     * @param Student $student
     * @return Response
     */
    public function show(Student $student): Response
    {
        Authorize::abortIfNot('student_show');

        return inertia('Admin/Student/Show', [
            'groups' => Group::select('id', 'name')->orderBy('name')->get(),
            'student' => new StudentResource($student)
        ]);
    }

    /**
     * @param StudentUpdateRequest $userUpdateRequest
     * @param Student $student
     * @return RedirectResponse
     */
    public function update(StudentUpdateRequest $userUpdateRequest, Student $student): RedirectResponse
    {
        Authorize::abortIfNot('student_update');

        $this->studentService->update($student, $userUpdateRequest->validated());

        return redirect()->route('admin.student.index');
    }

    /**
     * @param Student $student
     * @return RedirectResponse
     */
    public function destroy(Student $student): RedirectResponse
    {
        Authorize::abortIfNot('student_destroy');

        $this->studentService->delete($student);

        return redirect()->route('admin.student.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('student_destroy');

        $request->validate(['ids' => 'required|array|in:' . Student::get()->pluck('id')->join(',')]);

        $this->studentService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.student.index');
    }
}
