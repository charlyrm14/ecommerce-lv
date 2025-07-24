<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\{
    CategoryIndexRequest,
    CategoryStoreRequest
};
use App\Resources\Categories\NewCategoryResource;
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
            
            $query = Category::query()->with('childrenRecursive')->whereNull('parent_id')->where('status', 1);

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

    /**
     * Store a new category
     * Accepts a validated request with category data, creates the category,
     * loads its parent relationship, and returns the resource as JSON.
     *
     * @param \App\Http\Requests\CategoryStoreRequest $request with the validated request containing category creation data
     * @return \Illuminate\Http\JsonResponse response with the newly created category resource (201),
     * or an internal server error (500) if an exception occurs
     */
    public function store(CategoryStoreRequest $request): JsonResponse
    {
        try {

            $category = Category::create($request->validated());

            $category->load('parent');

            return response()->json([
                'data' => new NewCategoryResource($category)
            ], 201);
            
        } catch (\Throwable $e) {
            Log::error("Create category error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
