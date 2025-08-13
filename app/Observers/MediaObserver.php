<?php

namespace App\Observers;

use App\Models\Media;
use App\Services\FileService;
use Illuminate\Support\Facades\Log;

class MediaObserver
{
    /**
     * Handle the Media "created" event.
     */
    public function created(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "updated" event.
     */
    public function updated(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "deleted" event.
     */
    public function deleted(Media $media): void
    {
        try {
            
            foreach ($media->variants as $variant) {
                FileService::deleteFileFromPath($variant->file_path);
            }

            FileService::deleteFileFromPath($media->file_path);

        } catch (\Throwable $e) {
            Log::error("Error deleting media files: " . $e->getMessage());
        }
    }

    /**
     * Handle the Media "restored" event.
     */
    public function restored(Media $media): void
    {
        //
    }

    /**
     * Handle the Media "force deleted" event.
     */
    public function forceDeleted(Media $media): void
    {
        //
    }
}
