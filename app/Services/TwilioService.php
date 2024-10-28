<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Exception;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $this->from = env('TWILIO_PHONE_NUMBER');

        if (!$sid || !$authToken || !$this->from) {
            throw new Exception("Twilio configuration is incomplete.");
        }

        $this->client = new Client($sid, $authToken);
    }

    public function makeCall($to)
    {
        // Use the `url` helper to generate the full URL for the audio file
        $audioUrl = "https://phuse21.github.io/AudioFile/harvard.wav";

        Log::info("url => ", ['url' => $audioUrl]);

        try {
            $call = $this->client->calls->create(
                $to,                    // The phone number to call
                $this->from,            // Your Twilio number
                ["twiml" => "<Response><Play>{$audioUrl}</Play></Response>"]
            );

            Log::info("Call initiated: ", ['to' => $to, 'call_sid' => $call->sid]);

            return $call;

        } catch (\Throwable $th) {
            Log::error("Call failed: ", ['to' => $to, 'error' => $th->getMessage()]);
            throw new Exception("Twilio Error: " . $th->getMessage());
        }
    }
}