<?php

declare(strict_types=1);

namespace App\Resources\Products;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateProductResource extends JsonResource {

    public function toArray($product)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => (bool) $this->status,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'uuid' => $this->uuid,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'category' => $this->whenLoaded('category'),
            'brand' => $this->whenLoaded('brand')
        ];
    }
}
