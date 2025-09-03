<?php

declare(strict_types=1);

namespace App\Uploads;

use App\Uploads\Contracts\UploadStrategy;
use App\Uploads\Strategies\{
    DocumentUploadStrategy,
    ImageUploadStrategy
};
use Illuminate\Support\Str;

class UploadStrategyFactory
{
    public function make(string $mime): UploadStrategy
    {
        if (Str::startsWith($mime, 'image/')) {
            return app(ImageUploadStrategy::class);
        }

        return app(DocumentUploadStrategy::class);
    }
}
