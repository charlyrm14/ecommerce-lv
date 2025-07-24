<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\{
    CategoryIndexRequest
};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a list of categories
     *
     * If the `names` query parameter is provided, the list will be filtered
     * to include only categories that match the given names.
     *
     * @param \App\Http\Requests\CategoryIndexRequest $request with a possible filter name
     * @return \Illuminate\Http\JsonResponse JSON response with paged data or a no results found message
     */
    public function index(CategoryIndexRequest $request): JsonResponse
    {
        try {
            
            $query = Category::query()->with('childrenRecursive')->whereNull('parent_id');

            if ($request->filled('names')) {
                $names = explode(',', $request->query('names'));
                $query->whereIn('name', $names);
            }

            $categories = $query->paginate(15);

            if($categories->isEmpty()) {
                return response()->json(['message' => 'No results found'], 404);
            }

            return response()->json([
                'data' => $categories
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Category list error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
