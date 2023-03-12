<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\CategoryIndexRequest;
use App\Models\Category;
use App\Http\Requests\Admin\Category\CategoryStoreRequest;
use App\Http\Requests\Admin\Category\CategoryUpdateRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Services\Admin\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    protected CategoryService $categoryService;

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(CategoryIndexRequest $request): Response
    {
        Authorize::abortIfNot('category_index');

        $dataQuery = new DataQuery($request);

        $categories = $this->categoryService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Category/Index', [
            'categories' => CategoryResource::collection($categories),
            'query' => $dataQuery->query(),

            'canCategoryStore' => Authorize::any('category_store'),
            'canCategoryEdit' => Authorize::any('category_update'),
            'canCategoryDestroy' => Authorize::any('category_destroy'),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        Authorize::abortIfNot('category_store');

        return inertia('Admin/Category/Create');
    }

     /**
     * @param CategoryStoreRequest $categoryStoreRequest
     * @return RedirectResponse
     */
    public function store(CategoryStoreRequest $categoryStoreRequest): RedirectResponse
    {
        Authorize::abortIfNot('category_store');

        $category = $this->categoryService->store($categoryStoreRequest->validated());

        return redirect()->route('admin.category.edit', $category);
    }

    /**
     * @param Category  $category
     * @return Response
     */
    public function edit(Category $category): Response
    {
        Authorize::abortIfNot('category_update');

        return inertia('Admin/Category/Edit', [
            'category' => new CategoryResource($category),
            'canCategoryDestroy' => Authorize::any('category_destroy'),
        ]);
    }

    /**
     * @param CategoryUpdateRequest $categoryUpdateRequest
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(CategoryUpdateRequest $categoryUpdateRequest, Category $category): RedirectResponse
    {
        Authorize::abortIfNot('category_update');

        $category = $this->categoryService->update($category, $categoryUpdateRequest->validated());

        return redirect()->route('admin.category.index');
    }

    /**
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        Authorize::abortIfNot('category_destroy');

        $this->categoryService->delete($category);

        return redirect()->route('admin.category.index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        Authorize::abortIfNot('category_destroy');

        $request->validate(['ids' => 'required|array|in:' . Category::get()->pluck('id')->join(',')]);

        $this->categoryService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.category.index');
    }
}
