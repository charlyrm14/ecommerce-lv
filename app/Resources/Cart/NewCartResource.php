<?php

namespace App\Resources\Cart;

use App\Services\CartService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NewCartResource extends JsonResource {

    public function toArray($cart)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total_cart' => CartService::totalCart($this->cartItems),
            'products' => $this->whenLoaded('cartItems'),
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d')
        ];
    }
}
