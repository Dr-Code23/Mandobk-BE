<?php
namespace App\Traits;

trait dateTrait{
    public function changeDateFormat(string $date , string $format = 'Y / m / d'):string{
        return date($format, strtotime($date));
    }
}
