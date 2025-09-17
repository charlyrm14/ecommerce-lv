<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{
    Media,
    Product
};
use Illuminate\Support\Arr;

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
    public static function attachImages(Product $product, array $dataImages): void
    {
        if (!empty($dataImages)) {

            $imagesIds = array_column($dataImages, 'id');

            $images = Media::whereIn('id', $imagesIds)
                ->orWhereIn('parent_id', $imagesIds)
                ->get();
            
            if ($images->isNotEmpty()) {
                
                $mapIsMain = collect($dataImages)
                    ->pluck('is_main', 'id')
                    ->toArray();
                
                $images->each(function ($image) use ($mapIsMain) {
                    $image->is_main = $mapIsMain[$image->id] ?? false;

                    // Si es un thumbnail (tiene parent_id), hereda valor is_main del padre,
                    if ($image->parent_id && isset($mapIsMain[$image->parent_id])) {
                        $image->is_main = $mapIsMain[$image->parent_id];
                    }
                });

                $product->files()->saveMany($images);
            }
        }
    }
}
