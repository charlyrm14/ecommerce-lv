<?php

namespace App\Http\Controllers;

use App\Models\{
    Media,
    Product
};
use App\Http\Requests\{
    ProductIndexRequest,
    ProductStoreRequest,
    ProductUpdateRequest
};
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Resources\Products\NewProductResource;
use App\Resources\Products\ShowProductResource;
use App\Resources\Products\UpdateProductResource;


class ProductController extends Controller
{
    /**
     * Display a list of products
     *
     * If the `names` query parameter is provided, the list will be filtered
     * to include only brands that match the given names.
     *
     * @param \App\Http\Requests\ProductIndexRequest $request with a possible filter name
     * @return \Illuminate\Http\JsonResponse JSON response with paged data or a no results found message
     */
    public function index(ProductIndexRequest $request): JsonResponse
    {
        try {
            
            $query = Product::query()->where('status', 1);

            if ($request->filled('names')) {
                $names = explode(',', $request->query('names'));
                $query->where(function ($q) use ($names) {
                    foreach ($names as $name) {
                        $q->orWhere('name', 'LIKE', '%' . trim($name) . '%');
                    }
                });
            }

            $products = $query->paginate(15);

            if ($products->isEmpty()) {
                return response()->json(['message' => 'No results found'], 404);
            }

            return response()->json([
                'data' => $products
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Product list error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Store a new product
     * Accepts a validated request with product data, creates the product,
     * load a relationship with the category and brand that belongs the product, and returns the resource as JSON.
     *
     * @param \App\Http\Requests\ProductStoreRequest $request with the validated request containing product creation data
     * @return \Illuminate\Http\JsonResponse response with the newly created category resource (201),
     * or an internal server error (500) if an exception occurs
     */
    public function store(ProductStoreRequest $request): JsonResponse
    {
        try {
            
            DB::beginTransaction();

            $product = Product::create($request->validated());
            
            ProductService::attachImages($product, $request->images);

            DB::commit();

            $product->load(['category', 'brand', 'files']);

            return response()->json([
                'data' => new NewProductResource($product)
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Product create error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Update an existing product.
     *
     * Receives a validated request containing the updated product data,
     *
     * the category, load a relationship with the category and brand that belongs the product
     * and returns the resource as JSON..
     *
     * @param \App\Http\Requests\ProductUpdateRequest $request The validated request with update data.
     * @param int $id The ID of the product to be updated.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated category resource (200),
     * or an error response: 404 if not found, 422 if validation fails, or 500 on server error.
     */
    public function update(ProductUpdateRequest $request, int $id): JsonResponse
    {
        try {

            $product = Product::getById($id);

            if (!$product) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            $product->update($request->validated());
            $product->load(['category', 'brand']);

            return response()->json([
                'data' => new UpdateProductResource($product)
            ], 200);
            
        }  catch (\Throwable $e) {
            Log::error("Product update error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Display a detail of a product by uuid
     *
     * The product, loads their relationship with categories and brands that belongs to a product, and returns a JSON resource.
     *
     * @param string $uuid The uuid of the product to be displayed.
     *
     *  @return \Illuminate\Http\JsonResponse A JSON response with the product resource (200)
     * or an error response: 404 if not found or 500 on server error.
     *
     */
    public function show(string $uuid): JsonResponse
    {
        try {
            
            $product = Product::with([
                'category',
                'brand',
                'files' => function($query) {
                    $query->whereNull('parent_id')->with('variants');
                }
            ])->byUuid($uuid)->first();

            if (!$product) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            return response()->json([
                'data' => new ShowProductResource($product)
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Product detail error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Delete a product by id
     *
     * @param int $id The ID of the product to be deleted.
     *
     *  @return \Illuminate\Http\JsonResponse A JSON response with a message successfully product deleted (200)
     * or an error response: 404 if not found or 500 on server error.
     *
     */
    public function delete(int $id): JsonResponse
    {
        try {
            
            $product = Product::getById($id);

            if (!$product) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Product delete error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
