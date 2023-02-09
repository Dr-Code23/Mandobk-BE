<?php

return [
    'categories_commercial_name' => ['required', 'alpha_dash', 'max:255'],
    'categories_scientific_name' => ['required', 'alpha_dash', 'max:255'],
    'int' => ['required', 'numeric', 'min:1'],
    'double' => ['required', 'regex:'.config('regex.double')],
    'patch_number' => ['required', 'regex:'.config('regex.patch_number')],
    'provider' => ['required', 'alpha_dash', 'max:255'],
    'date' => ['required', 'date', 'after_or_equal:today'],
];
