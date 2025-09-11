<?php

declare(strict_types=1);

namespace App\Uploads\Strategies;

use App\Models\Media;
use App\Services\FileService;
use App\Uploads\Contracts\UploadStrategy;
use Illuminate\Http\UploadedFile;

class DocumentUploadStrategy implements UploadStrategy
{
    public function upload(UploadedFile $file): array
    {
        $document = FileService::storeOnDisk($file);

        $storeDoc = Media::create([
            'file_path' => $document['file_path'],
            'mime_type' => $document['mime_type'],
            'variant' => 'original'
        ]);

        return [

            'id' => $storeDoc->id,
            'file_path' => $storeDoc->file_path,
            'mime_type' => $storeDoc->mime_type,
            'variant' => 'original',
            'original_name' => $document['original_name'] ?? null,
            'variants' => []
        ];
    }
}