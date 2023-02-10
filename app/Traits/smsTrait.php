<?php

namespace App\Traits;

use Gr8Shivam\SmsApi\SmsApi;

trait smsTrait
{
    public function send()
    {
        // SmsApi::countryCode('20')->sendMessage('1203137613', 'Hello From Mohamed Attar');
        return smsapi()->countryCode('20')->sendMessage('1203137613', 'Hello From Mohamed Attar');
    }
}
