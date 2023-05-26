<?php

namespace App\Actions;

use App\Traits\DateTrait;

class PrepareNotificationPayload
{
    use DateTrait;

    public static function make(string $msg, string $fromDate)
    {
        $prepare = new PrepareNotificationPayload();

        return [
            'msg' => $msg,
            'from' => $prepare->messageSentFrom($fromDate),
        ];
    }
}
