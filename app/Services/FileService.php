<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileService {

    /**
     * Generates a folder name for the uploaded files based on the current date.
     *
     * The folder path format is: "uploads/YYYY/mm/dd/"
     * Example output: "uploads/2025/08/11/"
     *
     * @return string The folder name.
     */
    public static function getUploadFolderPath(): string
    {
        $now = Carbon::now();
        return 'uploads/' . $now->year . '/' . $now->format('m') . '/' . $now->format('d') . '/';
    }

    /**
     * Store an uploaded file in the public/uploads directory using a hashed name.
     *
     * The file is saved in a date-based subdirectory (e.g., uploads/2025/08/11/filename.ext).
     * Returns the relative file path on success, or null if an error occurs.
     * Throws an HttpResponseException if the file cannot be saved.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file instance.
     * @return string The relative path of the saved file
     *
     * Example return value:
     * uploads/2025/05/28/file.ext
     */
    public static function storeOnDisk(UploadedFile $file): ?string
    {
        try {

            $folder = self::getUploadFolderPath();

            $file_name = $file->hashName();
            
            $file->move(public_path($folder), $file_name);

            return $folder . $file_name;

        } catch (\Throwable $e) {
            Log::error("Error saving file on disk: " . $e->getMessage());

            throw new HttpResponseException(
                response()->json(['message' => 'Error saving file on disk'], 400)
            );
        }
    }
}
