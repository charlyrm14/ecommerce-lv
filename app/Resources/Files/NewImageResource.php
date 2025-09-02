<?php

declare(strict_types=1);

namespace App\Resources\Files;

use Illuminate\Http\Resources\Json\JsonResource;

class NewImageResource extends JsonResource {

    public function toArray($image)
    {
        return [
            'id' => $this->id,
            'original_name' => $this->original_name,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'mime_type' => $this->mime_type
        ];
    }
}
