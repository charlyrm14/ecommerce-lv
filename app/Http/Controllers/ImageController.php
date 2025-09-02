<?php

namespace App\Http\Controllers;

use App\Http\Requests\{
    ImageStoreRequest
};
use App\Models\{
    Media
};
use App\Services\{
    FileService,
    ImageService
};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Resources\Files\NewImageResource;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImageController extends Controller
{
    /**
     * Handle storing an uploaded image and its resized thumbnail.
     *
     * This method stores the original uploaded image to disk, generates a resized thumbnail,
     * and saves database records for both within a transaction to ensure consistency.
     *
     * @param  \App\Http\Requests\ImageStoreRequest  $request  The validated HTTP request containing the uploaded image file.
     * @return \Illuminate\Http\JsonResponse JSON response containing the stored image information for both original and thumbnail.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If there is a known error storing the image or saving records.
     * @throws \Throwable For any other unhandled errors, with rollback of DB transaction and logging.
     */
    public function store(ImageStoreRequest $request): JsonResponse
    {
        try {

            $image = $request->file('file');

            $original = FileService::storeOnDisk($image);
            $thumbnail = ImageService::generateResizedImage($original['file_path'], ['height' => 200, 'prefix' => 'thumbnail_']);
            
            DB::beginTransaction();

            $storeOriginalImage = Media::create([
                'file_path' => $original['file_path'],
                'mime_type' => $original['mime_type'],
                'image_variant' => 'original'
            ]);

            $storeThumbnailImage = Media::create([
                'file_path' => $thumbnail,
                'mime_type' => $original['mime_type'],
                'image_variant' => 'thumbnail',
                'parent_id' => $storeOriginalImage->id
            ]);

            FileService::addFileMetaData($storeOriginalImage, $original);
            FileService::addFileMetaData($storeThumbnailImage, $original);
            
            DB::commit();

            return response()->json([
                'data' => [
                    'sizes' => [
                        'original' => new NewImageResource($storeOriginalImage),
                        'thumbnail' => new NewImageResource($storeThumbnailImage)
                    ]
                ]
            ], 201);

        } catch (HttpResponseException $e) {

            Log::error("Image store in folder error: " . $e->getMessage());
            throw $e;

        } catch (\Throwable $e) {
            
            DB::rollBack();
            Log::error("Store image error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
