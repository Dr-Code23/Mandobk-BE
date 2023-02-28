<?php

namespace App\Rules;

use App\Models\V1\Product;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\InvokableRule;

class HasCommercialName implements InvokableRule
{
    use RoleTrait;
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        // Check If The Product Exists

        if (!Product::where('com_name', $value)->whereIn('user_id', $this->getSubUsersForUser())->first('id')) {
            $fail($this->translateErrorMessage('commercial_name', 'not_exists'));
        }
    }
}
