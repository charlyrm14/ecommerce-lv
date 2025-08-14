<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{
    BrandIndexRequest,
    BrandStoreRequest,
    BrandUpdateRequest
};
use App\Models\Brand;
use App\Resources\Brands\NewBrandResource;
use App\Resources\Brands\ShowBrandResource;
use App\Resources\Brands\UpdateBrandResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    /**
     * Display a list of brands
     *
     * If the `names` query parameter is provided, the list will be filtered
     * to include only brands that match the given names.
     *
     * @param \App\Http\Requests\BrandIndexRequest $request with a possible filter name
     * @return \Illuminate\Http\JsonResponse JSON response with paged data or a no results found message
     */
    public function index(BrandIndexRequest $request): JsonResponse
    {
        try {

            $query = Brand::query()->where('status', 1);

            if ($request->filled('names')) {
                $names = explode(',', $request->query('names'));
                $query->whereIn('name', $names);
            }

            $brands = $query->paginate(15);

            if ($brands->isEmpty()) {
                return response()->json(['message' => 'No results found'], 404);
            }

            return response()->json([
                'data' => $brands
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Brand list error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Create a new brand
     * Accepts a validated request with brand data, creates the brand
     *
     * @param \App\Http\Requests\BrandStoreRequest $request with the validated request containing brand creation data
     * @return \Illuminate\Http\JsonResponse response with the newly created brand resource (201),
     * or an internal server error (500) if an exception occurs
     */
    public function store(BrandStoreRequest $request): JsonResponse
    {
        try {
            
            $brand = Brand::create($request->validated());

            return response()->json([
                'data' => new NewBrandResource($brand)
            ], 201);

        } catch (\Throwable $e) {
            Log::error("Brand create error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Update an existing brand.
     *
     * Receives a validated request containing the updated brand data,
     *
     * @param \App\Http\Requests\BrandUpdateRequest $request The validated request with update data.
     * @param int $id The ID of the brand to be updated.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated brand resource (200),
     * or an error response: 404 if not found, 422 if validation fails, or 500 on server error.
     *
     */
    public function update(BrandUpdateRequest $request, int $id): JsonResponse
    {
        try {
            
            $brand = Brand::getById($id);
            
            if (!$brand) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            $brand->update($request->validated());

            return response()->json([
                'data' => new UpdateBrandResource($brand)
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Brand update error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Display a detail of a brand by slug
     *
     * The brand, loads their relationship with products and category that belongs to a product, and returns a JSON resource.
     *
     * @param string $id The slug of the brand to be displayed.
     *
     *  @return \Illuminate\Http\JsonResponse A JSON response with the category resource (200)
     * or an error response: 404 if not found or 500 on server error.
     *
     */
    public function show(string $slug): JsonResponse
    {
        try {

            $brand = Brand::bySlug($slug)->with('products.category')->first();
            
            if (!$brand) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            return response()->json([
                'data' => new ShowBrandResource($brand)
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Brand detail error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Delete a brand by id
     *
     * @param int $id The ID of the brand to be deleted.
     *
     *  @return \Illuminate\Http\JsonResponse A JSON response with a message successfully category deleted (200)
     * or an error response: 404 if not found or 500 on server error.
     *
     */
    public function delete(int $id): JsonResponse
    {
        try {

            $brand = Brand::getById($id);
            
            if (!$brand) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            $brand->delete();

            return response()->json([
                'message' => 'Brand deleted successfully'
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Brand delete error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
