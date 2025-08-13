<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileService {

    /**
     * Generate a date-based folder path for storing uploaded files under the "uploads" directory.
     *
     * The folder structure follows the pattern: "uploads/YYYY/mm/dd/"
     * where YYYY is the current year, mm is the zero-padded month, and dd is the zero-padded day.
     *
     * If the folder does not exist, it will be created with permissions 0755, including any necessary parent directories.
     *
     * @return string The relative folder path (e.g., "uploads/2025/08/12/").
     *
     * @throws \RuntimeException If the folder creation fails.
     */
    public static function generateUploadsFolderPath(): string
    {
        $now = Carbon::now();
        $folder = 'uploads/' . $now->year . '/' . $now->format('m') . '/' . $now->format('d') . '/';

        $fullPath = public_path($folder);

        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        return $folder;
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

            $folder = self::generateUploadsFolderPath();

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

    /**
     * Deletes a file from the filesystem given its relative or absolute path.
     *
     * This method checks if the file exists before attempting deletion.
     * If the file does not exist, it returns false.
     * If the file is successfully deleted, it returns true.
     *
     * @param string $pathFile The file path to delete.
     * @return bool True if the file was deleted successfully; false if the file did not exist.
     */
    public static function deleteFileFromPath(string $pathFile): bool
    {
        if (!File::exists($pathFile)) {
            return false;
        }

        File::delete($pathFile);

        return true;
    }
}
