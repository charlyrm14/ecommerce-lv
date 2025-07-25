<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{
    BrandIndexRequest,
    BrandStoreRequest
};
use App\Models\Brand;
use App\Resources\Brands\NewBrandResource;
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
}
