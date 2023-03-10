<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JobVacancy\JobVacancyIndexRequest;
use App\Models\JobVacancy;
use App\Http\Requests\Admin\JobVacancy\JobVacancyStoreRequest;
use App\Http\Requests\Admin\JobVacancy\JobVacancyUpdateRequest;
use App\Http\Resources\Admin\JobVacancyResource;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use App\Services\Admin\JobVacancyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class JobVacancyController extends Controller
{
    /**
     * @var JobVacancyService
     */
    protected JobVacancyService $jobVacancyService;

    /**
     * @param JobVacancyService $jobVacancyService
     */
    public function __construct(JobVacancyService $jobVacancyService)
    {
        $this->jobVacancyService = $jobVacancyService;
    }

    /**
     * @param JobVacancyIndexRequest $request
     * @return Response
     */
    public function index(JobVacancyIndexRequest $request): Response
    {
        Authorize::abortIfNot('job_vacancy_index');

        $dataQuery = new DataQuery($request);

        $jobVacancies = $this->jobVacancyService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('title'),
            $dataQuery->sort()
        );

        return inertia('Admin/JobVacancy/Index', [
            'jobVacancies' => JobVacancyResource::collection($jobVacancies),
            'query' => $dataQuery->query(),

            'canJobVacancyStore' => Authorize::any('job_vacancy_store'),
            'canJobVacancyEdit' => Authorize::any('job_vacancy_update'),
            'canJobVacancyDestroy' => Authorize::any('job_vacancy_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('job_vacancy_store');

        $students = Student::select('id', 'name')->orderBy('name')->get();
        $groups = Group::select('id', 'name')->orderBy('name')->get();
        $displayOptions = ['Usuário', 'Grupo'];

        return inertia('Admin/JobVacancy/Create', [
            'students' => $students,
            'groups' => $groups,
            'displayOptions' => $displayOptions
        ]);
    }

    /**
     * @param JobVacancyStoreRequest $jobVacancyStoreRequest
     * @return RedirectResponse
     */
    public function store(JobVacancyStoreRequest $jobVacancyStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('job_vacancy_store');

        $jobVacancy = $this->jobVacancyService->store($jobVacancyStoreRequest->validated());

        return redirect()->route('admin.job-vacancy.edit', $jobVacancy);
    }

    /**
     * @param JobVacancy $jobVacancy
     * @return Response
     */
    public function edit(JobVacancy $jobVacancy): Response
    {
        Authorize::abortIfNot('job_vacancy_update');

        $students = Student::select('id', 'name')->orderBy('name')->get();
        $groups = Group::select('id', 'name')->orderBy('name')->get();
        $displayOptions = ['Usuário', 'Grupo'];

        return inertia('Admin/JobVacancy/Edit', [
            'jobVacancy' => new JobVacancyResource($jobVacancy),
            'students' => $students,
            'groups' => $groups,
            'displayOptions' => $displayOptions,
            'canJobVacancyDestroy' => Authorize::any('job_vacancy_destroy'),
        ]);
    }

    /**
     * @param JobVacancy $jobVacancy
     * @return Response
     */
    public function show(JobVacancy $jobVacancy): Response
    {
        Authorize::abortIfNot('job_vacancy_show');

        $students = Student::select('id', 'name')->orderBy('name')->get();
        $groups = Group::select('id', 'name')->orderBy('name')->get();
        $displayOptions = ['Usuário', 'Grupo'];

        return inertia('Admin/JobVacancy/Show', [
            'jobVacancy' => new JobVacancyResource($jobVacancy),
            'students' => $students,
            'groups' => $groups,
            'displayOptions' => $displayOptions
        ]);
    }

    /**
     * @param JobVacancyUpdateRequest $jobVacancyUpdateRequest
     * @param JobVacancy $jobVacancy
     * @return RedirectResponse
     */
    public function update(JobVacancyUpdateRequest $jobVacancyUpdateRequest, JobVacancy $jobVacancy): RedirectResponse
    {
        Authorize::abortIfNot('job_vacancy_update');

        $jobVacancy = $this->jobVacancyService->update($jobVacancy, $jobVacancyUpdateRequest->validated());

        return redirect()->route('admin.job-vacancy.index');
    }

    /**
     * @param JobVacancy $jobVacancy
     * @return RedirectResponse
     */
    public function destroy(JobVacancy $jobVacancy): RedirectResponse
    {
        Authorize::abortIfNot('job_vacancy_destroy');

        $this->jobVacancyService->delete($jobVacancy);

        return redirect()->route('admin.job-vacancy.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('job_vacancy_destroy');

        $request->validate(['ids' => 'required|array|in:' . JobVacancy::get()->pluck('id')->join(',')]);

        $this->jobVacancyService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.job-vacancy.index');
    }
}
