<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email_tujuan, $subject, $pdf;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_tujuan, $subject, $pdf)
    {
         $this->email_tujuan = $email_tujuan;
         $this->subject = $subject;
         $this->pdf = $pdf;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mail::to($this->email_tujuan)->send(new SendEmail($this->subject, $this->pdf)); 
        dd($this->pdf);
    }
}
