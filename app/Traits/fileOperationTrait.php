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

    public function storeBarCodeSVG(string $directory, string $file_name): bool
    {
        if (!is_dir(__DIR__.'/../../storage/app/public/'.$directory)) {
            mkdir(__DIR__.'/../../storage/app/public/'.$directory);
        }
        $handle = fopen(__DIR__.'/../../storage/app/public/'.$directory.'/'.$file_name.'.svg', 'w');
        fwrite($handle, DNS1D::getBarcodeSVG("$file_name", 'CODABAR', showCode: false));
        fclose($handle);

        return true;
    }

    /**
     * Write A Json File For Testing.
     */
    public function writeAFileForTesting(string $directory, string $file_name, string $data): void
    {
        if (!is_dir(__DIR__.'/../../tests/responsesExamples/'.$directory)) {
            mkdir(__DIR__.'/../../tests/responsesExamples/'.$directory, recursive: true);
        }
        $handle = fopen(__DIR__.'/../../tests/responsesExamples/'.$directory."/$file_name".'.json', 'w');
        fwrite($handle, $data);
        fclose($handle);
    }
}