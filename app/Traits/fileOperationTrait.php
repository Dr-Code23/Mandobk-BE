<?php

namespace App\Traits;

trait fileOperationTrait
{
    /**
     *  Get Translation Content From the Lang Directory.
     */
    public function getWebTranslationFile(string $file_name, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $file_content = require_once __DIR__."/../../lang/$locale/Api/Web/V1/$file_name";

        return $file_content;
    }
}
