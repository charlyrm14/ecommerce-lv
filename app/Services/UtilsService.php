<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;

class UtilsService {

    /**
     * Generates a SKU (Stock Keeping Unit) string based on a name.
     *
     * The SKU consists of:
     * - A 3-letter uppercase prefix derived from the name (without spaces).
     * - A timestamp in the format YmdHis (year, month, day, hour, minute, second).
     * - A random 4-character uppercase alphanumeric string.
     *
     * Example output: "APP-20250724183045-K9XZ"
     *
     * @param string $name The name used to generate the SKU.
     * @return string The generated SKU.
     */
    public static function generateSku(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/\s+/', '', $name), 0, 3));
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(4));

        return "{$prefix}-{$timestamp}-{$random}";
    }
}
