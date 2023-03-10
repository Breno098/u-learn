<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Http\DataQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductIndexRequest;
use App\Models\Product;
use App\Http\Requests\Admin\Product\ProductStoreRequest;
use App\Http\Requests\Admin\Product\ProductUpdateRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Services\Admin\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    protected ProductService $productService;

    /**
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    /**
     * @param ProductIndexRequest $request
     * @return Response
     */
    public function index(ProductIndexRequest $request): Response
    {
        $dataQuery = new DataQuery($request);

        $products = $this->productService->index(
            $dataQuery->filters(),
            $dataQuery->rowsPerPage(),
            $dataQuery->orderBy('name'),
            $dataQuery->sort()
        );

        return inertia('Admin/Product/Index', [
            'products' => ProductResource::collection($products),
            'query' => $dataQuery->query(),
        ]);
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        return inertia('Admin/Product/Create');
    }

     /**
     * @param ProductStoreRequest $productStoreRequest
     * @return RedirectResponse
     */
    public function store(ProductStoreRequest $productStoreRequest): RedirectResponse
    {
        $product = $this->productService->store($productStoreRequest->validated());

        return redirect()->route('admin.product.edit', $product);
    }

     /**
     * @param Product  $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return inertia('Admin/Product/Show', [
            'product' => new ProductResource($product)
        ]);
    }

    /**
     * @param Product  $product
     * @return Response
     */
    public function edit(Product $product): Response
    {
        return inertia('Admin/Product/Edit', [
            'product' => new ProductResource($product)
        ]);
    }

    /**
     * @param ProductUpdateRequest $productUpdateRequest
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(ProductUpdateRequest $productUpdateRequest, Product $product): RedirectResponse
    {
        $product = $this->productService->update($product, $productUpdateRequest->validated());

        return redirect()->route('admin.product.index');
    }

    /**
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->route('admin.product.index');
    }

     /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroyMultiples(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'nullable|array|in:' . Product::get()->pluck('id')->join(','),
        ]);

        $this->productService->deleteMultiple($request->get('ids', []));

        return redirect()->route('admin.product.index');
    }
}
