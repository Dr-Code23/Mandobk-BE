<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait DateTrait
{
    public function changeDateFormat(string|null $date, string $format = 'Y / m / d'): string|null
    {
        return $date ? date($format, strtotime($date)) : null;
    }

    /**
     * Summary of addDaysToDate.
     *
     * @param mixed $date
     */
    public function addDaysToDate(int $days, $date = null): string
    {
        $date = $date ?? date('Y-m-d');

        return date('Y-m-d', strtotime($date . ' +' . $days . ' days'));
    }


    public function messageSentFrom(string $at)
    {
        $sentAt = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime($at)));
        $now = Carbon::createFromFormat('Y-m-d H:i:s', now());
        $diff = $now->diffInRealMinutes($sentAt);
        $res = '';
        if ($diff == 0) $res = 'Now';

        // Minutes
        else if ($diff >= 1 && $diff <= 59)
            $res = "$diff Minute" . ($diff == 1 ? '' : 's');

        // Hours
        else if ($diff >= 60 && $diff <= ((60 * 24) - 1)) {
            $diff =  (int)(($diff / 60));
            $res = "$diff Hour" . ($diff == 1 ? '' : 's');
        }

        // Days
        else if ($diff >= (60 * 24) && $diff <= ((60 * 24 * 30) - 1)) {
            $diff =  (int)($diff / (60 * 24));
            $res = "$diff Day" . ($diff == 1  ? '' : 's');
        }

        // Weeks
        else if ($diff >= (60 * 24 * 7) && $diff <= ((60 * 24 * 30) - 1)) {
            $diff =  (int)($diff / (60 * 24 * 7));
            $res = "$diff Week" . ($diff == 1  ? '' : 's');
        }

        // Months
        else if ($diff >= (60 * 24 * 30) && $diff <= ((60 * 24 * 30 * 12) - 1)) {
            $diff =  (int)($diff / (60 * 24 * 30));
            $res = "$diff Month" . ($diff == 1  ? '' : 's');
        }

        // Years
        else {
            $diff = (int)($diff /  (60 * 24 * 30 * 12));
            $res = "$diff Year" . ($diff == 1  ? '' : 's');
        }

        return $res;
    }
}
