<?php

namespace App\Rules\Api\Web\V1;

use App\Traits\translationTrait;
use Illuminate\Contracts\Validation\Rule;

class stringRule implements Rule
{
    use translationTrait;
    private string $attribute_name;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $attribute_name)
    {
        $this->attribute_name = $attribute_name;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match(config('regex.ar_en_num_symbols'), $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->translateErrorMessage($this->attribute_name, 'custom_string.invalid');
    }
}
