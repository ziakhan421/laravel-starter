<?php

namespace App\Services\SMS;

interface SMSService
{
    public function sendSMS($phone, $message);
}
