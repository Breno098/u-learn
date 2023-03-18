<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Module\ModuleIndexRequest;
use App\Http\Requests\Admin\Module\ModuleStoreRequest;
use App\Http\Requests\Admin\Module\ModuleUpdateRequest;
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
     * @param ModuleIndexRequest $request
     * @return Response
     */
    public function index(ModuleIndexRequest $request): Response
    {
        $dataQuery = new DataQuery($request);

        $modules = $this->moduleService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Module/Index', [
            'modules' => ModuleResource::collection($modules),
            'query' => $dataQuery->query(),

            'canModuleStore' => true || Authorize::any('module_store'),
            'canModuleEdit' => true || Authorize::any('module_update'),
            'canModuleDestroy' => true || Authorize::any('module_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        return inertia('Admin/Module/Create');
    }

    /**
     * @return Response
     */
    public function edit(Module $module): Response
    {
        return inertia('Admin/Module/Edit', [
            'module' => new ModuleResource($module),
        ]);
    }

    /**
     * @param ModuleStoreRequest $moduleStoreRequest
     * @return RedirectResponse
     */
    public function store(ModuleStoreRequest $moduleStoreRequest): RedirectResponse
    {
        $this->moduleService->store($moduleStoreRequest->validated());

        return redirect()->route('admin.module.index');
    }

    /**
     * @param ModuleUpdateRequest $moduleUpdateRequest
     * @param Module $module
     * @return RedirectResponse
     */
    public function update(ModuleUpdateRequest $moduleUpdateRequest, Module $module): RedirectResponse
    {
        $this->moduleService->update($module, $moduleUpdateRequest->validated());

        return redirect()->route('admin.module.index');
    }

    /**
     * @param Module $module
     * @return RedirectResponse
     */
    public function destroy(Module $module): RedirectResponse
    {
        $deleted = $this->moduleService->delete($module);

        // if ($deleted) {
        //     $this->moduleService->reorder($course, $course->modules->toArray());
        // }

        return redirect()->route('admin.module.index');
    }

    // /**
    //  * @param Request $request
    //  * @return RedirectResponse
    //  */
    // public function reorder(Course $course, Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'modules' => 'required|array',
    //         'modules.*.id' => 'required|exists:modules,id',
    //     ]);

    //     $this->moduleService->reorder($course, $request->get('modules', []));

    //     return redirect()->route('admin.course.module.index', $course);
    // }
}
