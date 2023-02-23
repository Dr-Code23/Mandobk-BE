<?php

namespace App\Services\Api\V1\Notifications;

use App\Models\User;
use App\Notifications\RegisterUserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

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
