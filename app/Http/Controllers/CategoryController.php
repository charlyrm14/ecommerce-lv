<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\{
    CategoryIndexRequest,
    CategoryStoreRequest,
    CategoryUpdateRequest
};
use App\Resources\Categories\{
    NewCategoryResource,
    ShowCategoryResource,
    UpdateCategoryResource
};
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    /**
     * Update an existing category.
     *
     * Receives a validated request containing the updated category data,
     * performs business logic validations (such as ensuring a category
     * does not belong to itself or to invalid parent categories), updates
     * the category, loads its parent relationship, and returns a JSON resource.
     *
     * @param \App\Http\Requests\CategoryUpdateRequest $request The validated request with update data.
     * @param int $id The ID of the category to be updated.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with the updated category resource (200),
     * or an error response: 404 if not found, 422 if validation fails, or 500 on server error.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If validation rules are violated.
     */
    public function update(CategoryUpdateRequest $request, int $id): JsonResponse
    {
        try {
            
            $category = Category::byId($id)->first();
        
            if (!$category) {
                return response()->json(['message' => 'Not found'], 404);
            }

            CategoryService::validateNotSelfParent($category->id, $request->parent_id);
            CategoryService::validateMainCategoryNotAssignedToMain($category->parent_id, $request->parent_id);
            CategoryService::validateParentIsNotSubcategory($request->parent_id);

            $category->update($request->validated());
            $category->load('parent');

            return response()->json([
                'data' => new UpdateCategoryResource($category)
            ], 200);

        } catch (HttpResponseException $e) {

            Log::error("Update category validation error: " . $e->getMessage());
            throw $e;

        } catch (\Throwable $e) {
            Log::error("Update category error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Display a detail of a category by slug
     *
     * @param string $slug The slug of the category to be displayed.
     *
     *  @return \Illuminate\Http\JsonResponse A JSON response with the category resource (200)
     * or an error response: 404 if not found or 500 on server error.
     *
     */
    public function show(string $slug): JsonResponse
    {
        try {

            $category = Category::with(['parent', 'childrenRecursive'])->bySlug($slug)->first();
        
            if (!$category) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            return response()->json([
                'data' => new ShowCategoryResource($category)
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Show detail category error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }

    /**
     * Delete a category by id
     *
     * @param int $id The ID of the category to be deleted.
     *
     *  @return \Illuminate\Http\JsonResponse A JSON response with a message successfully category deleted (200)
     * or an error response: 404 if not found or 500 on server error.
     *
     */
    public function delete(int $id): JsonResponse
    {
        try {

            $category = Category::with('parent')->byId($id)->first();
            
            if (!$category) {
                return response()->json(['message' => 'Resource not found'], 404);
            }

            $category->delete();

            return response()->json(['message' => 'Category deleted successfully'], 200);

        } catch (\Throwable $e) {
            Log::error("Delete category error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
