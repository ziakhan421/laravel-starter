<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $textSms;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $textSms)
    {
        $this->phone = $phone;
        $this->textSms = $textSms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \App\Facades\SMSGateway::sendSMS($this->phone, $this->textSms);
    }
}
