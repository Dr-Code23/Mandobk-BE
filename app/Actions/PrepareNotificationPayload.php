<?php

namespace App\Actions;

class PrepareNotificationPayload
{
    public static function make($data, $fromDate)
    {
        return [
            'data' => $data,
            'from' => $fromDate
        ];
    }
}
