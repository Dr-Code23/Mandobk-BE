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

    'accepted' => 'must be accepted.',
    'accepted_if' => 'must be accepted when :other is :value.',
    'active_url' => 'is not a valid URL.',
    'after' => 'لا بد ان يكون بعد :date.',
    'after_or_equal' => 'لا بد ان يكون في يساوي او بعد :date.',
    'alpha' => 'must only contain letters.',
    'alpha_dash' => 'must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'must only contain letters and numbers.',
    'array' => 'يجب ن يكون عباره عن array',
    'ascii' => 'must only contain single-byte alphanumeric characters and symbols.',
    'before' => 'يجب ان يكون قبل :date.',
    'before_or_equal' => 'يجب ان يكون يساوي او قبل :date.',
    'between' => [
        'array' => 'must have between :min and :max items.',
        'file' => 'يجب ان يكون ما بين :min و :max كيلوبايت.',
        'numeric' => 'يجب ان يكون محصور بين :min و :max.',
        'string' => 'يجب ان يكون ما بين :min و :max حروف.',
    ],
    'boolean' => 'يجب ان يكون قيمه منطقيه صحيحه',
    'confirmed' => 'وتاكيد كلمه السر غير متطابقين',
    'current_password' => 'The password is incorrect.',
    'date' => 'ليس تاريخ صحيح',
    'date_equals' => 'يحب ان  يساوي :date.',
    'date_format' => 'يجب ان يكون تنسيقه هو :format.',
    'decimal' => 'must have :decimal decimal places.',
    'declined' => 'must be declined.',
    'declined_if' => 'must be declined when :other is :value.',
    'different' => 'and :other must be different.',
    'digits' => 'must be :digits digits.',
    'digits_between' => 'يجب ان يكون بين :min و :max ارقام.',
    'dimensions' => 'has invalid image dimensions.',
    'distinct' => 'field has a duplicate value.',
    'doesnt_end_with' => 'may not end with one of the following: :values.',
    'doesnt_start_with' => 'may not start with one of the following: :values.',
    'email' => 'يجب ان يكون ايميل صحيح',
    'ends_with' => 'must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'مستخدم بالفعل',
    'not_exists' => 'غير موجود',
    'file' => 'ليس ملف',
    'filled' => 'field must have a value.',
    'gt' => [
        'array' => 'must have more than :value items.',
        'file' => 'must be greater than :value kilobytes.',
        'numeric' => 'must be greater than :value.',
        'string' => 'must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'must have :value items or more.',
        'file' => 'must be greater than or equal to :value kilobytes.',
        'numeric' => 'must be greater than or equal to :value.',
        'string' => 'must be greater than or equal to :value characters.',
    ],
    'image' => 'ليست صوره',
    'in' => 'الحقل:attribute غير صحيح',
    'in_array' => 'الحقل غير موجود في :other.',
    'integer' => 'يجب ان يكون رقم',
    'ip' => 'must be a valid IP address.',
    'ipv4' => 'must be a valid IPv4 address.',
    'ipv6' => 'must be a valid IPv6 address.',
    'json' => 'must be a valid JSON string.',
    'lowercase' => 'must be lowercase.',
    'lt' => [
        'array' => 'must have less than :value items.',
        'file' => 'must be less than :value kilobytes.',
        'numeric' => 'must be less than :value.',
        'string' => 'must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'must not have more than :value items.',
        'file' => 'must be less than or equal to :value kilobytes.',
        'numeric' => 'must be less than or equal to :value.',
        'string' => 'must be less than or equal to :value characters.',
    ],
    'mac_address' => 'must be a valid MAC address.',
    'max' => [
        'array' => 'must not have more than :max items.',
        'file' => 'لا يجب ان يكون اكبر من :max كيلوبايت.',
        'numeric' => 'لا يجب ان يكون اكبر من :max.',
        'string' => 'لا يجب ان يحتوي علي اكتر من :max احرف',
    ],
    'max_digits' => 'must not have more than :max digits.',
    'mimes' => 'يجب ان يكون امتداد الملف من الاتي: :values.',
    'mimetypes' => 'must be a file of type: :values.',
    'min' => [
        'array' => 'must have at least :min items.',
        'file' => 'لا يجب ان يكون اقل من :min كيلوبايت.',
        'numeric' => 'لا يجب ان يكون اقل من :min.',
        'string' => 'لا يجب ان يحتوي علي اقل من :min احرف',
    ],
    'min_digits' => 'must have at least :min digits.',
    'multiple_of' => 'must be a multiple of :value.',
    'not_in' => 'الحقل :attribute غير صحيح',
    'not_regex' => 'التنسيق خاطئ',
    'numeric' => 'يجب ان يكون رقم',
    'password' => [
        'letters' => 'يجب ان يحتوي علي حرف واحد علي الاقل',
        'min' => 'الحقل اقل من :min احرف',
        'mixed' => 'يجب ان يحتوي علي احرف صغيره وكبيره',
        'numbers' => 'يجب تواجد رقم واحد علي الاقل',
        'symbols' => 'يجب تواجد رمز واحد علي الاقل',
        'uncompromised' => 'Found on leaked data on the internet choose another one!',
    ],
    'present' => 'field must be present.',
    'prohibited' => 'field is prohibited.',
    'prohibited_if' => 'field is prohibited when :other is :value.',
    'prohibited_unless' => 'field is prohibited unless :other is in :values.',
    'prohibits' => 'field prohibits :other from being present.',
    'regex' => 'تنسيقه غير صحيح',
    'required' => 'مطلوب',
    'required_array_keys' => 'field must contain entries for: :values.',
    'required_if' => 'field is required when :other is :value.',
    'required_if_accepted' => 'field is required when :other is accepted.',
    'required_unless' => 'field is required unless :other is in :values.',
    'required_with' => 'field is required when :values is present.',
    'required_with_all' => 'field is required when :values are present.',
    'required_without' => 'field is required when :values is not present.',
    'required_without_all' => 'field is required when none of :values are present.',
    'same' => 'and :other must match.',
    'size' => [
        'array' => 'must contain :size items.',
        'file' => 'must be :size kilobytes.',
        'numeric' => 'must be :size.',
        'string' => 'must be :size characters.',
    ],
    'starts_with' => 'must start with one of the following: :values.',
    'string' => 'يجب ان يكون نص',
    'timezone' => 'must be a valid timezone.',
    'unique' => 'مستخدم بالفعل',
    'uploaded' => 'failed to upload.',
    'uppercase' => 'must be uppercase.',
    'url' => 'must be a valid URL.',
    'ulid' => 'must be a valid ULID.',
    'uuid' => 'must be a valid UUID.',
    'not_found' => 'غير موجود',
    'not_empty' => 'غير فارغ',
    'limited' => 'محدود الصرف ولا يمكن اخد كميه اكبر من 1',
    'invalid' => 'غير صحيح',
    'operation_failed' => 'Operation Failed',
    // Custom Error Messages
    'not_fully_numbers_symbols' => 'لا يجب ان يتكون من ارقام او رموز فقط',
    'custom_string' => [
        'invalid' => 'يجب ان يحتوي علي احرف عربيه او انجليزيه او احرف وارقام ',
    ],
    'username' => [
        'regex' => 'يجب ان يبدا بحرف وان يتراوح عدده من 6 الي 40',
    ],
    'commercial_name' => [
        'max' => 'لا يجب ان يكون اكثر من:max احرف',
    ],
    'scientific_name' => [
        'max' => 'لا يجب ان يكون اكثر من:max احرف',
    ],
    'quantity' => [
        'big' => 'يحتوي علي كميه اكبر من الموجوده واللي هي',
        'regex' => 'يجب ان يكون رقم صحيح وان يكون اكبر من صفر',
    ],
    'purchase_price' => [
        'regex' => 'يجب ان يكون رقم اكبر من صفر',
    ],
    'selling_price' => [
        'regex' => 'يجب ان يكون رقم اكبر من صفر',
    ],
    'bonus' => [
        'regex' => 'يجب ان يكون رقم اكبر من صفر',
    ],
    'patch_number' => [
        'regex' => 'Is Invalid',
    ],
    'concentrate' => [
        'number' => [
            'between' => 'يحب ان يكون رقم',
        ],
        'regex' => 'يجب ان يكون رقم اك',
    ],
    'entry_date' => [
        'date' => [
            'date_format' => 'يجب ان يكون تنسيقه هو :format',
        ],
    ],
    'expire_date' => [
        'date' => [
            'date_format' => 'يجب ان يكون تنسيقه هو :format',
            'after' => 'يجب ان يكون بعد '.__(config('app.web_v1').'/Dashboard/dataEntryTranslationFile.entry_date'),
        ],
    ],
    'human_resource' => [
        'requried_if' => 'Is required when user_status is attendance',
        'after' => 'يجب ان يكون قبل :date',
    ],
    'discount' => [
        'regex' => 'غير صالخ',
    ],
    'created' => 'تم انشاءه بنجاح',
    'updated' => 'تم تحديثه بنجاح',
    'deleted' => 'تم حذفه بنجاح',
    'limited_products_with_big_quantity' => 'محدود الصرف ولا يمكن اخد كميه اكبر من 1',
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
