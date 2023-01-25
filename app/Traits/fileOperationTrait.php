<?php

namespace App\Traits;

use Milon\Barcode\DNS1D;

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

    public function storeBarCodeSVG(string $file_name): bool
    {
        if (!is_dir(__DIR__.'/../../storage/app/public/categories/')) {
            mkdir(__DIR__.'/../../storage/app/public/categories/');
        }
        $handle = fopen(__DIR__.'/../../storage/app/public/categories/'.$file_name.'.svg', 'w');
        fwrite($handle, DNS1D::getBarcodeSVG("$file_name", 'CODABAR', showCode: false));
        fclose($handle);

        return true;
    }
}
