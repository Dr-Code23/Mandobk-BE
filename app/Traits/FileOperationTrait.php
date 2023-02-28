<?php

namespace App\Traits;

use Milon\Barcode\DNS1D;

trait FileOperationTrait
{
    use StringTrait;

    /**
     *  Get Translation Content From the Lang Directory.
     */
    public function getWebTranslationFile(string $file_name, string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        return require_once __DIR__ . "/../../lang/$locale/" . config('app.web_v1') . "/$file_name.php";
    }

    public function storeBarCodeSVG(string $directory, string $code, string $file_name): bool
    {
        $handle = fopen(__DIR__ . '/../../storage/app/public/' . $directory . '/' . $file_name . '.svg', 'w');
        fwrite($handle, DNS1D::getBarcodeSVG("$code", 'CODABAR'));
        fclose($handle);

        return true;
    }

    /**
     * Write A Json File For Testing.
     */
    public function writeAFileForTesting(string $directory, string $file_name, string $data): void
    {
        if (config('test.store_response')) {
            if (!is_dir(__DIR__ . '/../../tests/responsesExamples/' . $directory)) {
                mkdir(__DIR__ . '/../../tests/responsesExamples/' . $directory, recursive: true);
            }
            $handle = fopen(__DIR__ . '/../../tests/responsesExamples/' . $directory . "/$file_name" . '.json', 'w');
            fwrite($handle, $data);
            fclose($handle);
        }
    }

    /**
     * Delete A Barcode.
     *
     * @return bool
     */
    public function deleteBarCode(string $file_name = null, string $directory = 'Dashboard')
    {
        if (is_file(__DIR__ . '/../../storage/app/public/' . $directory . '/' . $file_name)) {
            unlink(__DIR__ . '/../../storage/app/public/' . $directory . '/' . $file_name);

            return true;
        }

        return false;
    }

    public function deleteImage(string $path): bool
    {
        if (!is_file(__DIR__ . '/../../storage/app/public/' . $path)) return true;
        if (is_file(__DIR__ . '/../../storage/app/public/' . $path)) {
            unlink(__DIR__ . '/../../storage/app/public/' . $path);

            return true;
        }

        return false;
    }
}
