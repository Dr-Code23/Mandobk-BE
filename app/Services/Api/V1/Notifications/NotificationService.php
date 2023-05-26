<?php

namespace App\Services\Api\V1\Notifications;

use App\Models\User;

class NotificationService
{
    public function index($request)
    {
        if ($request->type == 'unread') {
            // Find Unread Notifications
            return User::find(auth()->id())->unreadNotifications;
        }

        return User::find(auth()->id())->notifications;
    }
}
