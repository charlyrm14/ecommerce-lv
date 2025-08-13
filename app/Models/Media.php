<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function variants(): HasMany
    {
        return $this->hasMany(Media::class, 'parent_id');
    }

    /**
     * Create and persist a Media record with the given file path, MIME type, image variant,
     * and optionally a parent Media ID.
     *
     * This method stores metadata of an uploaded media file in the database.
     * It supports linking different image variants (e.g., thumbnail, medium)
     * to a parent/original image via the $parentId parameter.
     *
     * @param string $filePath The relative path to the stored file.
     * @param string $mimeType The MIME type of the file (e.g., "image/png", "file/xlsx").
     * @param string|null $image_variant A label representing the image variant
     * (e.g., "original", "medium", "thumbnail").
     * @param int|null $parentId The ID of the parent Media record if this file is a
     * variant of another image, or null if it is the original.
     *
     * @return \App\Models\Media     The newly created Media model instance.
     */
    public static function storeMediaRecord(
        string $filePath,
        string $mimeType,
        string|null $image_variant = null,
        int|null $parentId = null
    ): Media
    {
        return self::create([
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'image_variant' => $image_variant,
            'parent_id' => $parentId
        ]);
    }
}
