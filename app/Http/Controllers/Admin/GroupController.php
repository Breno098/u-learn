<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Group\GroupIndexRequest;
use App\Models\Group;
use App\Http\Requests\Admin\Group\GroupStoreRequest;
use App\Http\Requests\Admin\Group\GroupUpdateRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Models\Permission;
use App\Models\Student;
use App\Models\User;
use App\Services\Admin\GroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;


class GroupController extends Controller
{
     /**
     * @var GroupService
     */
    protected GroupService $groupService;

    /**
     * @param GroupService $groupService
     */
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @param GroupIndexRequest $request
     * @return Response
     */
    public function index(GroupIndexRequest $request): Response
    {
        Authorize::abortIfNot('group_index');

        $dataQuery = new DataQuery($request);

        $groups = $this->groupService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Group/Index', [
            'groups' => GroupResource::collection($groups),
            'query' => $dataQuery->query(),

            'canGroupStore' => Authorize::any('group_store'),
            'canGroupEdit' => Authorize::any('group_update'),
            'canGroupShow' => Authorize::any('group_show'),
            'canGroupDestroy' => Authorize::any('group_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('group_store');

        $users = User::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Group/Create', [
            'users' => $users
        ]);
    }

    /**
     * @param GroupStoreRequest $groupStoreRequest
     * @return RedirectResponse
     */
    public function store(GroupStoreRequest $groupStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('group_store');

        $this->groupService->store($groupStoreRequest->validated());

        return redirect()->route('admin.group.index');
}

    /**
     * @param Group $group
     * @return Response
     */
    public function edit(Group $group): Response
    {
        Authorize::abortIfNot('group_update');

        $students = Student::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Group/Edit', [
            'group' => new GroupResource($group),
            'students' => $students,
            'canGroupDestroy' => Authorize::any('group_destroy'),
        ]);
    }

    /**
     * @param Group $group
     * @return Response
     */
    public function show(Group $group): Response
    {
        Authorize::abortIfNot('group_show');

        $students = Student::select('id', 'name')->orderBy('name')->get();

        return inertia('Admin/Group/Show', [
            'group' => new GroupResource($group),
            'students' => $students
        ]);
    }

    /**
     * @param GroupUpdateRequest $groupUpdateRequest
     * @param Group $group
     * @return RedirectResponse
     */
    public function update(GroupUpdateRequest $groupUpdateRequest, Group $group): RedirectResponse
    {
        Authorize::abortIfNot('group_update');

        $group = $this->groupService->update($group, $groupUpdateRequest->validated());

        return redirect()->route('admin.group.index');
    }

    /**
     * @param Group $group
     * @return RedirectResponse
     */
    public function destroy(Group $group): RedirectResponse
    {
        Authorize::abortIfNot('group_destroy');

        $this->groupService->delete($group);

        return redirect()->route('admin.group.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('group_destroy');

        $request->validate(['ids' => 'required|array|in:' . Group::get()->pluck('id')->join(',')]);

        $this->groupService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.group.index');
    }
}
