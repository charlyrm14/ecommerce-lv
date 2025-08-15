<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{
    Media,
    Product
};

class ProductService {

    /**
     * Attach images (thumbnails and their originals) to a product.
     *
     * Given an array of image IDs (usually thumbnails), this function fetches
     * the images along with their parent images (originals, if any) in a single query
     * and attaches them to the provided product.
     *
     * @param Product $product The product model to attach images to.
     * @param array<int> $imageIds Array of image IDs (thumbnails) to attach.
     *
     * @return void
     */
    public static function attachImages(Product $product, array $imageIds): void
    {
        if (!empty($imageIds)) {

            $images = Media::whereIn('id', $imageIds)
                ->orWhereIn('id', function ($query) use ($imageIds) {
                    $query->select('parent_id')
                        ->from('media')
                        ->whereIn('id', $imageIds)
                        ->whereNotNull('parent_id');
            })->get();
            
            if ($images->isNotEmpty()) {
                $product->images()->saveMany($images);
            }
        }
    }
}
