<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\RoleIndexRequest;
use App\Http\Requests\Admin\Role\RoleStoreRequest;
use App\Http\Requests\Admin\Role\RoleUpdateRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;


class RoleController extends Controller
{
     /**
     * @var RoleService
     */
    protected RoleService $roleService;

    /**
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @param RoleIndexRequest $request
     * @return Response
     */
    public function index(RoleIndexRequest $request): Response
    {
        Authorize::abortIfNot('role_index');

        $dataQuery = new DataQuery($request);

        $roles = $this->roleService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Role/Index', [
            'roles' => RoleResource::collection($roles),
            'query' => $dataQuery->query(),

            'canRoleStore' => Authorize::any('role_store'),
            'canRoleEdit' => Authorize::any('role_update'),
            'canRoleDestroy' => Authorize::any('role_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('role_store');

        return inertia('Admin/Role/Create', [
            'permissions' => Permission::select('id', 'label', 'level')->orderBy('level')->orderBy('label')->get()->groupBy('level'),
        ]);
    }

    /**
     * @param RoleStoreRequest $roleStoreRequest
     * @return RedirectResponse
     */
    public function store(RoleStoreRequest $roleStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('role_store');

        $role = $this->roleService->store($roleStoreRequest->validated());

        return redirect()->route('admin.role.edit', $role);
}

    /**
     * @param Role $role
     * @return Response
     */
    public function edit(Role $role): Response
    {
        Authorize::abortIfNot('role_update');

        return inertia('Admin/Role/Edit', [
            'role' => new RoleResource($role),
            'permissions' => Permission::select('id', 'label', 'level')->orderBy('level')->orderBy('label')->get()->groupBy('level'),
            'canRoleDestroy' => Authorize::any('role_destroy'),
        ]);
    }

    /**
     * @param Role $role
     * @return Response
     */
    public function show(Role $role): Response
    {
        Authorize::abortIfNot('role_show');

        return inertia('Admin/Role/Show', [
            'role' => new RoleResource($role),
            'permissions' => Permission::select('id', 'label', 'level')->orderBy('level')->orderBy('label')->get()->groupBy('level'),
        ]);
    }

    /**
     * @param RoleUpdateRequest $roleUpdateRequest
     * @param Role $role
     * @return RedirectResponse
     */
    public function update(RoleUpdateRequest $roleUpdateRequest, Role $role): RedirectResponse
    {
        Authorize::abortIfNot('role_update');

        $role = $this->roleService->update($role, $roleUpdateRequest->validated());

        return redirect()->route('admin.role.index');
    }

    /**
     * @param Role $role
     * @return RedirectResponse
     */
    public function destroy(Role $role): RedirectResponse
    {
        Authorize::abortIfNot('role_destroy');

        $this->roleService->delete($role);

        return redirect()->route('admin.role.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('role_destroy');

        $request->validate(['ids' => 'required|array|in:' . Role::get()->pluck('id')->join(',')]);

        $this->roleService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.role.index');
    }
}
