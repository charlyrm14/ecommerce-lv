<?php

declare(strict_types=1);

namespace App\Uploads\Strategies;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Uploads\Contracts\UploadStrategy;
use App\Services\{
    FileService,
    ImageService
};

class ImageUploadStrategy implements UploadStrategy
{

    public function upload(UploadedFile $file): array
    {
        $original = FileService::storeOnDisk($file);

        $thumbnail = ImageService::generateResizedImage(
            $original['file_path'],
            ['height' => 200, 'prefix' => 'thumbnail_']
        );

        return DB::transaction(function () use ($original, $thumbnail) {

            $storeOriginal = Media::create([
                'file_path' => $original['file_path'],
                'mime_type' => $original['mime_type'],
                'variant' => 'original'
            ]);

            $storeThumbnail = Media::create([
                'file_path' => $thumbnail,
                'mime_type' => $original['mime_type'],
                'variant' => 'thumbnail',
                'parent_id' => $storeOriginal->id,
            ]);

            return [
                'id' => $storeOriginal->id,
                'file_path' => $storeOriginal->file_path,
                'mime_type' => $storeOriginal->mime_type,
                'variant' => 'original',
                'original_name' => $original['original_name'] ?? null,
                'variants' => [
                    [
                        'id' => $storeThumbnail->id,
                        'variant' => 'thumbnail',
                        'file_path' => $storeThumbnail->file_path,
                        'size' => null,
                        'width' => null,
                        'height' => null,
                        'resolution' => null,
                        'original_name' => $original['original_name'] ?? null
                    ]
                ]
            ];
        });
    }
}
