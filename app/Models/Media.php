<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    protected $table = 'media';

    /**
     * The function `imageable()` returns a polymorphic relationship for the model.
     *
     * @return MorphTo The `imageable()` function is returning a MorphTo relationship. This function is
     * typically used in Laravel Eloquent models to define a polymorphic relationship, allowing the
     * model to belong to multiple other models.
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
