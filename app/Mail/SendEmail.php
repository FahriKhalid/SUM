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
    
    public $id_skpp, $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($subject, $id_skpp)
    {
        $this->id_skpp = $id_skpp;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(SkppService $SkppService)
    { 

        $pdf = $SkppService->suratSKPP($this->id_skpp); 

        return $this->from('SETIAGUNG USAHA MANDIRI')
                    ->subject($this->subject)
                    ->view('email.email_template') 
                    ->attachData($pdf->output(), 'skpp-'.date('dmY').'.pdf');
    }
}
