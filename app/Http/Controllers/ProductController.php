<?php

namespace App\Http\Controllers;

use App\Http\Requests\{
    ProductIndexRequest
};
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a list of products
     *
     * If the `names` query parameter is provided, the list will be filtered
     * to include only brands that match the given names.
     *
     * @param \App\Http\Requests\ProductIndexRequest $request with a possible filter name
     * @return \Illuminate\Http\JsonResponse JSON response with paged data or a no results found message
     */
    public function index(ProductIndexRequest $request): JsonResponse
    {
        try {
            
            $query = Product::query()->where('status', 1);

            if ($request->filled('names')) {
                $names = explode(',', $request->query('names'));
                $query->where(function ($q) use ($names) {
                    foreach ($names as $name) {
                        $q->orWhere('name', 'LIKE', '%' . trim($name) . '%');
                    }
                });
            }

            $products = $query->paginate(15);

            if ($products->isEmpty()) {
                return response()->json(['message' => 'No results found'], 404);
            }

            return response()->json([
                'data' => $products
            ], 200);

        } catch (\Throwable $e) {
            Log::error("Product list error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
