<?php

namespace App\Helpers;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioHelper
{
    // Send OTP via Twilio
    public static function sendOtp($country_code,$phoneNumber,$message)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

        try {
            $client = new Client($sid, $token);

            $client->messages->create(
                '+'.$country_code. $phoneNumber,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            Log::error('Twilio OTP error: ' . $e->getMessage());
            throw new \Exception('Error sending OTP: ' . $e->getMessage());
        }
    }

    public static function StatusMessage($country_code,$phone,$message)
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');
        Log::info("Phone". $phone . " -- Country" . $country_code);
        try {
            $client = new Client($sid, $token);

            $client->messages->create(
                  '+'.$country_code.$phone,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            Log::error('Twilio error: ' . $e->getMessage() . ' - Code: ' . $e->getCode());
            throw new \Exception('Error sending : ' . $e->getMessage(). ' - Code: ' . $e->getCode());
        }
    }
}
