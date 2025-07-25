<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\{
    BrandIndexRequest
};
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    /**
     * Display a list of categories
     *
     * If the `names` query parameter is provided, the list will be filtered
     * to include only categories that match the given names.
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
            Log::error("Category list error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
