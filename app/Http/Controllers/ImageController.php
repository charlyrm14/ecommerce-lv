<?php

namespace App\Http\Controllers;

use App\Http\Requests\{
    ImageStoreRequest
};
use App\Models\Media;
use App\Services\FileService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    /**
     * Store an uploaded image file into the uploads folder
     *
     *
     * @param \App\Http\Requests\ImageStoreRequest $request The request containing the image
     * @return \Illuminate\Http\JsonResponse JSON response with a stored image path,
     * including file path and size type (original)
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException If there is an error storing the file.
     */
    public function store(ImageStoreRequest $request): JsonResponse
    {
        try {

            $image = $request->file('file');
            $mimeType = $image->getMimeType();

            $pathImage = FileService::storeOnDisk($request->file('file'));

            $originalImage = Media::storeFile($pathImage, $mimeType, 'original');

            return response()->json([
                'data' => [
                    'original' => $originalImage
                ]
            ], 201);

        } catch (HttpResponseException $e) {

            Log::error("Image store in folder error: " . $e->getMessage());
            throw $e;

        } catch (\Throwable $e) {
            Log::error("Store image error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
