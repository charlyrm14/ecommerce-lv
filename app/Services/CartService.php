<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartService {

    /**
     * Validates unit prices against actual product prices.
     *
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $productsModel
     * @param \Illuminate\Support\Collection<int, array{product_id: int, quantity: int, unit_price: float}> $requestedProducts
     */
    public static function validatePriceByProduct(?Collection $productsModel, SupportCollection $requestedProducts): void
    {
        foreach ($productsModel as $product) {
            
            $productToValidate = $requestedProducts->where('product_id', $product->id)->first();

            if(!$productToValidate) {
                continue;
            }

            if((float) $productToValidate['unit_price'] !== (float) $product->price) {
                throw new HttpResponseException(
                    response()->json(['message' => "Invalid unit price for product ID {$product->id}"], 422)
                );
            }
        }
    }

    /**
     * Validates quantity against actual product stock.
     *
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $productsModel
     * @param \Illuminate\Support\Collection<int, array{product_id: int, quantity: int, unit_price: float}> $requestedProducts
     */
    public static function validateStockByProduct(?Collection $productsModel, SupportCollection $requestedProducts): void
    {
        foreach ($productsModel as $product) {
            
            $productToValidate = $requestedProducts->where('product_id', $product->id)->first();

            if(!$productToValidate) {
                continue;
            }

            if((int) $productToValidate['quantity'] > (int) $product->stock) {
                throw new HttpResponseException(
                    response()->json(['message' => "Is not more stock for product ID {$product->id}"], 422)
                );
            }
        }
    }

    
    /**
     * Calculate the total amount per product based on a quantity and unit price
     *
     * @param \Illuminate\Support\Collection $requestedProducts. Each item include:
     *     - product_id (int)
     *     - quantity (int|float)
     *     - unit_price (float)
     * @return array Return an array of products with the total amount per product
     */
    public static function calculateTotalPerProduct(SupportCollection $requestedProducts): array
    {
        return $requestedProducts->map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ];
        })->toArray();
    }

    /**
     * Calculate the total amount per cart based on a list of products
     *
     * @param \Illuminate\Support\Collection $products. A collection of products
     * from which the total amount will be calculated.
     *
     * @return float float The total amount of the cart, rounded to two decimal places
     */
    public static function totalCart(Collection $products): float
    {
        return (float)number_format($products->sum('total'), 2, '.', '');
    }
}
