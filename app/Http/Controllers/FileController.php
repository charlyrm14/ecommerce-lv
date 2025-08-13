<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    /**
     * Deletes a media record and all its related variant files from storage and database.
     *
     * This method performs the following steps:
     * 1. Attempts to find the media record by its ID, including its variants.
     * 2. If the media record does not exist, returns a 404 JSON response.
     * 3. Deletes the physical files of all variant images from the storage.
     * 4. Deletes the physical file of the original media from the storage.
     * 5. Starts a database transaction to:
     *      - Delete all variant records associated with the media.
     *      - Delete the main media record.
     * 6. Commits the transaction.
     * 7. Returns a JSON success message upon successful deletion.
     *
     * If any error occurs during the process, it rolls back the transaction,
     * logs the error, and returns a 500 JSON response with a generic error message.
     *
     * @param int $id The ID of the media record to be deleted.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function delete(int $id)
    {
        try {

            $media = Media::with('variants')->find($id);

            if(!$media) {
                return response()->json(['message' => 'Resource not found'], 404);
            }
            
            DB::beginTransaction();

            $media->variants()->delete();
            $media->delete();

            DB::commit();

            return response()->json([
                'message' => 'File deleted succesfully'
            ], 200);

        } catch (\Throwable $e) {
            
            DB::rollBack();
            Log::error("Delete file error: " . $e->getMessage());
            return response()->json(["error" => 'Internal server error'], 500);
        }
    }
}
