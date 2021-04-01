<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\SkppService;
use App\SKPP;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels; 
    
    public $subject, $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($subject, $pdf)
    { 
        $this->subject = $subject;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
        $subject = $this->subject;

        return $this->from('niagahoster@setiagung.com')
                    ->subject($subject)
                    ->view('email.email_template', compact('subject')) 
                    ->attachData($this->pdf->output(), $subject.'.pdf');
    }
}
