<?php

declare(strict_types=1);

namespace App\Resources\Files;

use Illuminate\Http\Resources\Json\JsonResource;

class NewImageResource extends JsonResource {

    public function toArray($image)
    {
        return [
            'id' => $this->id,
            'file_path' => $this->file_path
        ];
    }
}
