<?php

namespace App\Traits;

use App\Http\Resources\Api\V1\Translation\translationResource;

trait translationTrait
{
    use fileOperationTrait;

    /**
     * Translate A Resource.
     */
    public function translateResource(string $file_name, string $locale = null): translationResource
    {
        // Get The Translation File
        $login_view = $this->getWebTranslationFile($file_name, $locale);

        return new translationResource($login_view);
    }

    /**
     * Return Custom Error Message Vor Validation.
     */
    public function translateErrorMessage(string $name, string $key): string
    {
        return __('messages.'.$name).' '.__('validation.'.$key);
    }

    public function translateWord(string $word)
    {
        return __('messages.'.$word);
    }
}
