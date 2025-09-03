<?php

declare(strict_types=1);

namespace App\Uploads\Contracts;

use Illuminate\Http\UploadedFile;

interface UploadStrategy
{
    /**
     * Upload file and return a generic response with variants of the file
     *
     * @return array {
     *  data: array {
     *      id: int,
     *      type: string,
     *      mime_type: string,
     *      variants: array<string, array {
     *          id: int,
     *          file_path: string,
     *          size?: int,
     *          width?: int,
     *          height?: int,
     *          resolution?: string,
     *          original_name?: string
     *     }>
     *  }
     * }
     */
    public function upload(UploadedFile $file): array;
}
