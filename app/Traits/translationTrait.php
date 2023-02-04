<?php

namespace App\Traits;

use App\Http\Resources\Api\Web\V1\Translation\translationResource;

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

    public function translateErrorMessage(string $name, string $key)
    {
        return __('messages.'.$name).' '.__('validation.'.$key);
    }

    public function translateWord(string $word_path)
    {
        return __(config('app.web_v1').'/'.$word_path);
    }
}
