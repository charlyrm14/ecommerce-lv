<?php

declare(strict_types=1);

namespace App\Resources\Categories;

use Illuminate\Http\Resources\Json\JsonResource;

class NewCategoryResource extends JsonResource {

    public function toArray($category)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->slug,
            'status' => (bool) $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'parent' => $this->whenLoaded('parent', function () {
                return new self($this->parent);
            }),
        ];
    }
}
