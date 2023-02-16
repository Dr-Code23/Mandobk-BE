<?php

namespace App\Rules;

use App\Models\V1\Role;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\InvokableRule;

class RoleExists implements InvokableRule
{
    use Translatable;
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __invoke($attribute, $value, $fail): void
    {
        if (!Role::where('id', $value)->value('id')) {
            $fail($this->translateErrorMessage('role', 'not_exists'));
        }
    }
}
