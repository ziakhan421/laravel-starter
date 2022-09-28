<?php

namespace App\Services\SMS;

class TextlocalSMSServiceImplement implements SMSService
{
    public function sendSMS($phone, $message)
    {

        $apiKey = urlencode('Your apiKey');
        $sender = urlencode('Jims Autos');
        $message = rawurlencode($message);

        $numbers = $phone;

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // Send the POST request with cURL
        $ch = curl_init('https://api.txtlocal.com/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
