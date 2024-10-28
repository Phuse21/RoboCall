<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;
    protected $audioUrl;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        $this->from = env('TWILIO_PHONE_NUMBER');
        $this->audioUrl = env('TWILIO_AUDIO_URL');
    }

    public function makeCall($to)
    {
        return $this->client->calls->create(
            $to,                    // The phone number to call
            $this->from,            // Your Twilio number
            ["twiml" => "<Response><Play>{$this->audioUrl}</Play></Response>"]
        );
    }
}