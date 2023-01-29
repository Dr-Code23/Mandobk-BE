<?php

namespace App\Traits;

trait StringTrait
{
    /**
     * Prepare the string for validation.
     */
    public function sanitizeString(string $val): string
    {
        return trim(htmlspecialchars($val));
    }

    public function strLimit(string $val, int $limit = 30): string
    {
        return \Illuminate\Support\Str::limit($val, $limit);
    }
}
