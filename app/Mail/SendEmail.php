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
    
    public $subject, $pdf, $lampiran;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($subject, $pdf, $lampiran = null)
    { 
        $this->subject = $subject;
        $this->pdf = $pdf;
        $this->lampiran = $lampiran;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
        $subject = $this->subject;

        $email = $this->from('setiagung@setiagung.com')
                    ->subject($subject)
                    ->view('email.email_template', compact('subject'))
                    ->attachData($this->pdf->output(), $subject.'.pdf'); 
         
        if(count($this->lampiran) > 0)
        {
            foreach ($this->lampiran as $value) {
                $email->attach($value["url_file"]);
            }
        }

        return $email;
    }
}
