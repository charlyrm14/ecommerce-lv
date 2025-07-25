<?php

declare(strict_types=1);

namespace App\Resources\Brands;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowBrandResource extends JsonResource {

    public function toArray($brand)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => (bool) $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'products' => $this->whenLoaded('products')
        ];
    }
}
