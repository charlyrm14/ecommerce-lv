<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

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
     * Returns an associative array with metadata about the saved file, or null if an error occurs.
     * Throws an HttpResponseException if the file cannot be saved.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file instance.
     * @return array|null An array containing file information:
     *  - folder (string): The relative folder path where the file was stored.
     *  - file_name (string): The hashed file name assigned by Laravel.
     *  - file_path (string): The full relative path of the stored file.
     *  - original_name (string): The original client-provided file name.
     *
     * Example return value:
     * [
     *   "folder" => "uploads/2025/08/14/",
     *   "file_name" => "xEne7BlxV0H6prhRUxPfj0ioe7NshuNo2Z0EvCiN.jpg",
     *   "file_path" => "uploads/2025/08/14/xEne7BlxV0H6prhRUxPfj0ioe7NshuNo2Z0EvCiN.jpg",
     *   "original_name" => "profile_photo.jpg"
     *   "mime_type" => "image/jpeg"
     * ]
     */
    public static function storeOnDisk(UploadedFile $file): array
    {
        try {

            $folder = self::generateUploadsFolderPath();

            $original_name = $file->getClientOriginalName();
            $mime_type = $file->getMimeType();
            $file_name = $file->hashName();

            $file->move(public_path($folder), $file_name);

            return [
                'folder' => $folder,
                'file_name' => $file_name,
                'file_path' => $folder . $file_name,
                'original_name' => $original_name,
                'mime_type' => $mime_type
            ];

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

    public static function addFileMetaData(Media $media, array $metadata): void
    {
        $media->file_name = $metadata['file_name'] ?? null;
        $media->original_name = $metadata['original_name'] ?? null;
    }
}
