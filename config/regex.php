<?php

return [
    'username' => '/^[a-zA-Z][a-zA-Z0-9]{6,40}$/i',
    'en' => '/^[a-zA-Z0-9_$-]+$/i',
    'patch_number' => '/^[0-9]+-[0-9]+-[0-9]{1,}-[0-9]{1,}$/',
    'integer' => '/^[1-9]([0-9]+)?$/',
    'double' => '/^(0|([1-9][0-9]*))(\\.[0-9]+)?$/',
    'not_fully_numbers_symbols' => '/^[0-9_\-\)\(\*\&\^\%\$\#\!\@\.\,\:\'\"\\/\+\=\<\>\}\{\[\]]+$/i',
    // Matching Arabic Letters , english letters ,numbers and some symbols ./\_-
    'ar_en_num_symbols' => "/^[\u0621-\u064A\ u0660-\u0669 a-zA-z0-9_\-\:\|\\\/ \.\,]+$/iu",
];
