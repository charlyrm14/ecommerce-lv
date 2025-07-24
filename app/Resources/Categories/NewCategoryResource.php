<?php

declare(strict_types=1);

namespace App\Resources\Categories;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NewCategoryResource extends JsonResource {

    public function toArray($category)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'status' => (bool) $this->status,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'parent' => $this->whenLoaded('parent', function () {
                return new self($this->parent);
            }),
        ];
    }
}
