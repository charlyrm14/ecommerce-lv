<?php

namespace App\Models;

use App\Observers\MediaObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([MediaObserver::class])]
class Media extends Model
{
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'file_path',
        'mime_type',
        'image_variant',
        'parent_id',
        'mediaable_id',
        'mediaable_type'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d',
            'updated_at' => 'datetime:Y-m-d',
        ];
    }

    /**
     * The function `mediaable()` returns a polymorphic relationship for the model.
     *
     * @return MorphTo The `mediaable()` function is returning a MorphTo relationship. This function is
     * typically used in Laravel Eloquent models to define a polymorphic relationship, allowing the
     * model to belong to multiple other models.
     */
    public function mediaable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The variants function returns a collection of Media instances associated with the current model
     * through a hasMany relationship.
     *
     * @return HasMany A relationship method named "variants" is being returned. This method defines a
     * one-to-many relationship using the Laravel Eloquent ORM. It specifies that the current model has
     * many instances of the "Media" model where the "parent_id" column in the "Media" table matches
     * the primary key of the current model.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Media::class, 'parent_id');
    }
}
