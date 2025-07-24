<?php

declare(strict_types=1);

namespace App\Resources\Categories;

use Illuminate\Http\Resources\Json\JsonResource;

class UpdateCategoryResource extends JsonResource {

    public function toArray($category)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'parent_id' => $this->parent_id,
            'status' => (bool) $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => $this->whenLoaded('parent', function () {
                return new self($this->parent);
            }),
        ];
    }
}
