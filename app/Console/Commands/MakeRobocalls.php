<?php

namespace App\Console\Commands;

use App\Models\Contacts;
use Illuminate\Console\Command;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class MakeRobocalls extends Command
{
    protected $signature = 'make:robocalls';
    protected $description = 'Make robocalls to contacts who are not already called';
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    public function handle()
    {
        Contacts::where('status', 'not_called') // Check for contacts with status 'not_called'
            ->chunk(100, function ($contacts) {
                foreach ($contacts as $contact) {
                    try {
                        // Make the call using TwilioService
                        $this->twilioService->makeCall($contact->phoneNumber);

                        // Update contact status to called
                        // $contact->update(['status' => 'called']); // Mark as called
    
                        $this->info("Call initiated to {$contact->phoneNumber}");

                        // Sleep for 5 seconds before making the next call
                        sleep(5);
                    } catch (\Exception $e) {
                        // Log and display error
                        Log::error("Failed to call {$contact->phoneNumber}: " . $e->getMessage());
                        $this->error("Failed to call {$contact->phoneNumber}: " . $e->getMessage());
                    }
                }
            });

        $this->info('Robocalls completed.');
    }
}