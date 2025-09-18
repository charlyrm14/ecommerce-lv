<?php

declare(strict_types=1);

namespace App\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginUserResource extends JsonResource{

    public function toArray($user)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
