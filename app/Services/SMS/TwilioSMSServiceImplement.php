<?php

namespace App\Services\SMS;

class TwilioSMSServiceImplement implements SMSService
{
    public function sendSMS($phone, $message)
    {
        $ID = '1234567890abcdef1234567890abcdef12';
        $token = '1234567890abcdef1234567890abcdef';
        $service = 'AB1234567890abcdef1234567890abcdef';
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $ID . '/Messages.json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $ID . ':' . $token);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            'To=' . rawurlencode('+' . $phone) .
            '&MessagingServiceSid=' . $service .
            //'&From=' . rawurlencode('+18885550000') .
            '&Body=' . rawurlencode($message));

        $resp = curl_exec($ch);
        curl_close($ch);
        return json_decode($resp, true);
    }
}
