<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryService {

    /**
     * Validates that a category does not reference itself as its own parent.
     *
     * @param int      $categoryId  The ID of the category being updated or created.
     * @param int|null $parentId    The ID of the parent category (nullable).
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the category is assigned to itself.
     *
     * @return void
     */
    public static function validateNotSelfParent(int $categoryId, ?int $parentId): void
    {
        if ($categoryId === $parentId) {
            throw new HttpResponseException(
                response()->json(['message' => 'A category cannot belong to itself'], 422)
            );
        }
    }

    /**
     * Validates that a main category is not assigned to another main category.
     *
     * A category without a parent (main category) should not be nested under
     * another category that is also a main category.
     *
     * @param int|null $currentParentId The current parent_id of the category (nullable).
     * @param int|null $newParentId The ID of the parent category being assigned (nullable).
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If a main category is assigned to another main category.
     *
     * @return void
     */
    public static function validateMainCategoryNotAssignedToMain(?int $currentParentId, ?int $newParentId): void
    {
        $targetParent = Category::find($newParentId);
        $isCurrentMain = is_null($currentParentId);
        $isNewParentMain = !is_null($newParentId) && is_null($targetParent?->parent_id);

        if($isCurrentMain && $isNewParentMain) {
            throw new HttpResponseException(
                response()->json(['message' => 'A main category cannot belong to another main category'], 422)
            );
        }
    }

    /**
     * Validates that a main category is not assigned to a subcategory.
    *
    * This check only runs if a non-null parent ID is provided.
    *
    * @param int|null $parentId The ID of the parent category (nullable).
    *
    * @throws \Illuminate\Http\Exceptions\HttpResponseException If the parent category is a subcategory.
    *
    * @return void
     */
    public static function validateParentIsNotSubcategory(?int $parentId): void
    {
        if (!is_null($parentId)) {
            $parentCategory = Category::byId($parentId)->first();

            if (!$parentCategory) {
                throw new HttpResponseException(
                    response()->json(['message' => 'Parent category not found'], 404)
                );
            }

            if(!is_null($parentCategory->parent_id)) {
                throw new HttpResponseException(
                    response()->json(['message' => 'A category cannot belongs to a subcategory'], 422)
                );
            }
        }
    }
}
