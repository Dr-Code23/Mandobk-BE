<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class NotificationTypeExists implements InvokableRule
{
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $type = $value;
        $type = config('notifications.types')[$type];
        if ($type) {
            $type = 'Google';
            // Check If User Is Admin User
        }
    }
}
