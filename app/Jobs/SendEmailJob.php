<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use \App\Mail\AutomaticEmail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
     protected $details;
    /**
     * Create a new job instance.
     */
     public function __construct($details)
    {

        $this->details = [
        'email' => $details['email'],
        'subject' => $details['subject'],
        'message' => $details['message'],
    ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
       $email = new AutomaticEmail($this->details);
       Mail::to($this->details['email'])->send($email);

    }
}
