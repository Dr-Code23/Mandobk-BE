<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

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

    public function encrptString(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function decryptString(string $encrypted): string
    {
        return Crypt::decryptString($encrypted);
    }

    public function setPercisionForFloatString(string $val): string
    {
        return number_format((float) $val, 1, '.', '');
    }
}
