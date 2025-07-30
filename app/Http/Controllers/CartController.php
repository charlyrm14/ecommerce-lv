<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Models\Cart;
use App\Models\Product;
use App\Resources\Cart\NewCartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Store a new cart
     * Accepts a validated request with cart data, creates the cart,
     * loads its cart items relationship, and returns the resource as JSON.
     *
     * @param \App\Http\Requests\CartStoreRequest $request with the validated request containing cart creation data
     * @return \Illuminate\Http\JsonResponse response with the newly created cart resource (201),
     * or an internal server error (500) if an exception occurs
     */
    public function store(CartStoreRequest $request): JsonResponse
    {
        try {
            
            $cartData = Arr::only($request->validated(), ['user_id']);

            $requestedProducts = collect($request->validated()['products']);
            $productIds = collect($requestedProducts)->pluck('product_id')->all();
            $products = Product::whereIn('id', $productIds)->get();

            if($products->isEmpty()) {
                return response()->json(['message' => 'Not products results'], 404);
            }
            
            CartService::validatePriceByProduct($products, $requestedProducts);
            CartService::validateStockByProduct($products, $requestedProducts);

            $dataCartItems = CartService::calculateTotalPerProduct($requestedProducts);

            DB::beginTransaction();

            $cart = Cart::firstOrCreate(array_merge($cartData, ['status' => 'open']));
            Cart::storeItems($cart, $dataCartItems);

            DB::commit();

            $cart->load('cartItems');

            return response()->json([
                'data' => new NewCartResource($cart)
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Cart store error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
