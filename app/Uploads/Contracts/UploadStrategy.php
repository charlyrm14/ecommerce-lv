<?php

declare(strict_types=1);

namespace App\Uploads\Contracts;

use Illuminate\Http\UploadedFile;

interface UploadStrategy
{
    /**
     * Upload file and return a generic response with variants of the file
     *
     * @return
     *  data: array {
     *      id: int,
     *      file_path: string,
     *      mime_type: string,
     *      variant: string,
     *      original_name: string,
     *      variants array {
     *          id: int,
     *          variant: string,
     *          file_path: string,
     *          size?: int,
     *          width?: int,
     *          height?: int,
     *          resolution?: string,
     *          original_name?: string
     *      }
     * }
     */
    public function upload(UploadedFile $file): array;
}
