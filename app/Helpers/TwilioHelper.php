<?php

namespace App\Helpers;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioHelper
{
    // Send OTP via Twilio
    public static function sendOtp($country_code,$phoneNumber,$message)
    {
        // Retrieve Twilio credentials from the environment file
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER'); // Your Twilio phone number

        try {
            // Initialize the Twilio client
            $client = new Client($sid, $token);

            // Compose the OTP message


            // Send the OTP message via Twilio
            $client->messages->create(
                '+'.$country_code. $phoneNumber, // User's phone number
                [
                    'from' => $twilioPhoneNumber, // Your Twilio phone number
                    'body' => $message
                ]
            );
        } catch (\Exception $e) {
            Log::error('Twilio OTP error: ' . $e->getMessage());

            // Optionally, rethrow or handle differently
            throw new \Exception('Error sending OTP: ' . $e->getMessage());
        }
    }
}
