<?php

declare(strict_types=1);

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\FileService;

class ImageService {

    private const DEFAULT_HEIGHT = 200;
    private const DEFAULT_PREFIX = 'resized_';

    /**
     * Generates a resized version of an image given its public path and saves it to the uploads folder.
     *
     * The resized image will have a specified height while maintaining aspect ratio,
     * and the filename will be prefixed as specified in the options.
     * The resized image is saved immediately in the public uploads directory under a date-based folder.
     *
     * @param string $publicImagePath The relative public path to the original image (e.g., 'uploads/2025/08/12/image.jpg').
     * @param array $options Optional settings for resizing:
     *  - 'height' (int): Desired height of the resized image. Defaults to self::DEFAULT_HEIGHT.
     *  - 'prefix' (string): Prefix for the resized image filename. Defaults to self::DEFAULT_PREFIX.
     *
     * @return string The relative path to the resized image within the uploads directory.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If the image cannot be resized or saved.
     */
    public static function generateResizedImage(string $publicImagePath, array $options = []): string
    {
        try {

            $height = $options['height'] ?? self::DEFAULT_HEIGHT;
            $prefix = $options['prefix'] ?? self::DEFAULT_PREFIX;

            $uploadFolder = FileService::generateUploadsFolderPath();
            $originalImageName = basename($publicImagePath);
            $newResizedImage = $prefix . $originalImageName;
            $fullNewPath = public_path($uploadFolder . $newResizedImage);
            
            Image::read(public_path($publicImagePath))
                ->scaleDown(height: $height)
                ->save($fullNewPath);

            return $uploadFolder . $newResizedImage;

        } catch (\Throwable $e) {
            Log::error("Error generating resized image: " . $e->getMessage());

            throw new HttpResponseException(
                response()->json(['message' => 'Error generating resized image'], 400)
            );
        }
    }
}
