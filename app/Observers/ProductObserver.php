<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Services\UtilsService;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Product $product): void
    {
        $product->uuid = (string) Str::uuid();
        $product->sku = UtilsService::generateSku($product->name);
    }
}
