<?php

namespace App\Services\Api\V1\Notifications;

class NotificationService
{
    public function index($request)
    {
        return $request->all();
    }
}
