<?php

namespace App\Console\Commands;

use App\Models\Contacts;
use Illuminate\Console\Command;
use App\Services\TwilioService;

class MakeRobocalls extends Command
{
    protected $signature = 'make:robocalls';
    protected $description = 'Make robocalls to contacts in the database who are not already called';
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    public function handle()
    {
        $contacts = Contacts::all(); // Assuming you have a 'contacts' table with phone numbers

        foreach ($contacts as $contact) {
            try {
                $this->twilioService->makeCall($contact->phone_number);
                $this->info("Call initiated to {$contact->phone_number}");
            } catch (\Exception $e) {
                $this->error("Failed to call {$contact->phone_number}: " . $e->getMessage());
            }
        }

        $this->info('Robocalls completed.');
    }
}