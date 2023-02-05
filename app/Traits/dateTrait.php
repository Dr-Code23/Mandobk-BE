<?php

namespace App\Traits;

trait dateTrait
{
    public function changeDateFormat(string $date, string $format = 'Y / m / d'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Summary of addDaysToDate.
     *
     * @param mixed $date
     */
    public function addDaysToDate(int $days, $date = null): string
    {
        $date = $date ?? date('Y-m-d');

        return date('Y-m-d', strtotime($date.' +'.$days.' days'));
    }
}
