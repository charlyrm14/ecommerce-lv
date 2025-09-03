<?php

declare(strict_types=1);

namespace App\Uploads;

use Illuminate\Http\UploadedFile;
use App\Uploads\UploadStrategyFactory;
use Illuminate\Support\Facades\Log;

class UploadManager
{
    public function __construct(
        protected UploadStrategyFactory $factory
    ){}

    public function handle(UploadedFile $file): array
    {
        $mime = $file->getMimeType();

        /** @var UploadStrategy $strategy */
        $strategy = $this->factory->make($mime);

        try {
            
            return $strategy->upload($file);

        } catch (\Throwable $e) {

            Log::error("Upload Manager: " . $e->getMessage());
            abort(500, 'Error uploading file');

        }
    }
}
