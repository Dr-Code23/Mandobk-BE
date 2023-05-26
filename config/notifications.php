<?php

use App\Notifications\OrderStatusNotification;
use App\Notifications\RegisterUserNotification;

return [
    // 'types' => [
    //     'newUserRegister' => '1',
    // ],
    'newUserRegister' => ['ceo', 'data_entry', 'company'],
    'messages' => [
        RegisterUserNotification::class => 'New User Registered',
        OrderStatusNotification::class => 'An Action Take On Your Order',
    ],
    'types' => [
        RegisterUserNotification::class => 'register',
        OrderStatusNotification::class => 'order',
    ],
];
