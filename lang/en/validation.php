<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ' must be accepted.',
    'accepted_if' => ' must be accepted when :other is :value.',
    'active_url' => ' is not a valid URL.',
    'after' => ' must be a date after :date.',
    'after_or_equal' => ' must be a date after or equal to :date.',
    'alpha' => ' must only contain letters.',
    'alpha_dash' => ' must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => ' must only contain letters and numbers.',
    'array' => ' must be an array.',
    'ascii' => ' must only contain single-byte alphanumeric characters and symbols.',
    'before' => ' must be a date before :date.',
    'before_or_equal' => ' must be a date before or equal to :date.',
    'between' => [
        'array' => ' must have between :min and :max items.',
        'file' => ' must be between :min and :max kilobytes.',
        'numeric' => 'must be between :min and :max.',
        'string' => ' must be between :min and :max characters.',
    ],
    'boolean' => 'Can Be True , false , 0 or 1',
    'confirmed' => ' confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => ' is not a valid date.',
    'date_equals' => ' must be a date equal to :date.',
    'date_format' => 'does not match the format :format.',
    'decimal' => ' must have :decimal decimal places.',
    'declined' => ' must be declined.',
    'declined_if' => ' must be declined when :other is :value.',
    'different' => ' and :other must be different.',
    'digits' => ' must be :digits digits.',
    'digits_between' => 'must be between :min and :max digits.',
    'dimensions' => ' has invalid image dimensions.',
    'distinct' => ' field has a duplicate value.',
    'doesnt_end_with' => ' may not end with one of the following: :values.',
    'doesnt_start_with' => ' may not start with one of the following: :values.',
    'email' => ' must be a valid email address.',
    'ends_with' => ' must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'Is Already In Use',
    'not_exists' => 'Is not exists',
    'file' => ' must be a file.',
    'filled' => ' field must have a value.',
    'gt' => [
        'array' => ' must have more than :value items.',
        'file' => ' must be greater than :value kilobytes.',
        'numeric' => ' must be greater than :value.',
        'string' => ' must be greater than :value characters.',
    ],
    'gte' => [
        'array' => ' must have :value items or more.',
        'file' => ' must be greater than or equal to :value kilobytes.',
        'numeric' => ' must be greater than or equal to :value.',
        'string' => ' must be greater than or equal to :value characters.',
    ],
    'image' => ' must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => ' field does not exist in :other.',
    'integer' => ' must be an integer.',
    'ip' => ' must be a valid IP address.',
    'ipv4' => ' must be a valid IPv4 address.',
    'ipv6' => ' must be a valid IPv6 address.',
    'json' => ' must be a valid JSON string.',
    'lowercase' => ' must be lowercase.',
    'lt' => [
        'array' => ' must have less than :value items.',
        'file' => ' must be less than :value kilobytes.',
        'numeric' => ' must be less than :value.',
        'string' => ' must be less than :value characters.',
    ],
    'lte' => [
        'array' => ' must not have more than :value items.',
        'file' => ' must be less than or equal to :value kilobytes.',
        'numeric' => ' must be less than or equal to :value.',
        'string' => ' must be less than or equal to :value characters.',
    ],
    'mac_address' => ' must be a valid MAC address.',
    'max' => [
        'array' => ' must not have more than :max items.',
        'file' => ' must not be greater than :max kilobytes.',
        'numeric' => 'Cannot Be Greater Than :max.',
        'string' => 'Cannot Be Greater Than :max characters',
    ],
    'max_digits' => ' must not have more than :max digits.',
    'mimes' => 'must be a file of type: :values.',
    'mimetypes' => ' must be a file of type: :values.',
    'min' => [
        'array' => ' must have at least :min items.',
        'file' => ' must be at least :min kilobytes.',
        'numeric' => ' must be at least :min.',
        'string' => 'Cannot Be Less Than :min Characters',
    ],
    'min_digits' => ' must have at least :min digits.',
    'multiple_of' => ' must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => ' format is invalid.',
    'numeric' => 'must be a number.',
    'password' => [
        'letters' => ' letter',
        'min' => ':attribute Is Less than :min characters',
        'mixed' => 'Must Contain at least one upper and lower Character',
        'numbers' => 'Contain At least one number',
        'symbols' => 'Contain at least one symbol',
        'uncompromised' => 'Found on leaked data on the internet choose another one!',
    ],
    'present' => ' field must be present.',
    'prohibited' => ' field is prohibited.',
    'prohibited_if' => ' field is prohibited when :other is :value.',
    'prohibited_unless' => ' field is prohibited unless :other is in :values.',
    'prohibits' => ' field prohibits :other from being present.',
    'regex' => ' format is invalid.',
    'required' => 'Cannot Be Empty',
    'required_array_keys' => ' field must contain entries for: :values.',
    'required_if' => 'field is required when :other is :value.',
    'required_if_accepted' => ' field is required when :other is accepted.',
    'required_unless' => ' field is required unless :other is in :values.',
    'required_with' => ' field is required when :values is present.',
    'required_with_all' => ' field is required when :values are present.',
    'required_without' => ' field is required when :values is not present.',
    'required_without_all' => ' field is required when none of :values are present.',
    'same' => ' and :other must match.',
    'size' => [
        'array' => ' must contain :size items.',
        'file' => ' must be :size kilobytes.',
        'numeric' => ' must be :size.',
        'string' => ' must be :size characters.',
    ],
    'starts_with' => ' must start with one of the following: :values.',
    'string' => ' must be a string.',
    'timezone' => ' must be a valid timezone.',
    'unique' => ' has already been taken.',
    'uploaded' => ' failed to upload.',
    'uppercase' => ' must be uppercase.',
    'url' => ' must be a valid URL.',
    'ulid' => ' must be a valid ULID.',
    'uuid' => ' must be a valid UUID.',
    'not_found' => 'Not Found',
    'not_empty' => 'Not Empty',
    'limited' => 'Is limited and cannot take more than one',
    'invalid' => 'Is not valid',
    'operation_failed' => 'Operation Failed',
    // Custom Error Messages
    'not_fully_numbers_symbols' => 'Cannot be fully numbers or symbols',
    'custom_string' => [
        'invalid' => 'Can Only Contain arabic letters , english letters , numbers and symbols ',
    ],
    'username' => [
        'regex' => 'Must Start With One Letter , Must Be Between 6 and 40 alpha_numeric characters',
    ],
    'commercial_name' => [
        'max' => 'Cannot Be Greater Than :max Characters',
    ],
    'scientific_name' => [
        'max' => 'Cannot Be Greater Than :max Characters',
    ],
    'quantity' => [
        'big' => 'Has Total Bigger Than Existing which is ',
        'regex' => 'Must Be A Valid Number And Greater Than Zero',
    ],
    'purchase_price' => [
        'regex' => 'Must Be A Valid Double Value And Greater Than Zero',
    ],
    'selling_price' => [
        'regex' => 'Must Be A Valid Double Value And Greater Than Zero',
    ],
    'bonus' => [
        'regex' => 'Must Be A Valid Double Value And Greater Than Zero',
    ],
    'patch_number' => [
        'regex' => 'Is Invalid',
    ],
    'concentrate' => [
        'number' => [
            'between' => 'Must Be A Valid Double Value',
        ],
        'regex' => 'Must Be A Valid Double Value And Greater Than Zero',
    ],
    'provider' => [
        'max' => 'Cannot Be Greater Than :max Characters',
    ],
    'limited' => [
        'boolean' => 'Cannot be true , false , 0 or 1',
    ],
    'entry_date' => [
        'date' => [
            'date_format' => 'Must Be In :format Format',
        ],
    ],
    'expire_date' => [
        'date' => [
            'date_format' => 'Must Be In :format Format',
            'after' => 'Must Be After '.__(config('app.web_v1').'/Dashboard/dataEntryTranslationFile.entry_date'),
        ],
    ],
    'human_resource' => [
        'requried_if' => 'Is required when user_status is attendance',
        'after' => 'Must be after :date',
    ],
    'discount' => [
        'regex' => 'format is invalid',
    ],
    'created' => 'Created Successfully',
    'updated' => 'Updated Successfully',
    'deleted' => 'Deleted Successfully',
    'limited_products_with_big_quantity' => ' Is Limited And Have Quantity More than 1',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];
