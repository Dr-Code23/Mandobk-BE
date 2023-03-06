<?php

use App\Notifications\OrderStatusNotification;
use App\Notifications\RegisterUserNotification;

return [
    // 'types' => [
    //     'newUserRegister' => '1',
    // ],
    'newUserRegister' => ['ceo', 'data_entry', 'company'],
    'messages' => [
        RegisterUserNotification::class => 'New User Registered'
    ],
    'types' => [
        RegisterUserNotification::class => 'register',
        OrderStatusNotification::class => 'order'
    ]
];
