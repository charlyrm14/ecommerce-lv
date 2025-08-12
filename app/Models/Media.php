<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    /**
     * Create and persist a Media record with the given file path, MIME type, and size label.
     *
     * This method stores the metadata of an uploaded media file in the database.
     *
     * @param string $filePath The relative path to the stored file.
     * @param string $mimeType The MIME type of the file (e.g., "image/png", "file/xlsx").
     * @param string $size A label representing the size variant (e.g., "original", "medium", "thumbnail").
     *
     * @return \App\Models\Media The newly created Media model instance.
     */
    public static function storeFile(string $filePath, string $mimeType, string $size): Media
    {
        return self::create([
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'size' => $size
        ]);
    }
}
