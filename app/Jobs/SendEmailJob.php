<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels; 
use App\Services\SkppService;
use App\Mail\SendEmail;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email_tujuan;
    protected $modul;
    protected $id;
    protected $lampiran;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_tujuan, $modul, $id, $lampiran)
    {
        $this->email_tujuan = $email_tujuan;
        $this->modul = $modul; 
        $this->id = $id;
        $this->lampiran = $lampiran;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SkppService $SkppService)
    {   
        if($this->modul == "SKPP") {
            $pdf = $SkppService->suratSKPP($this->id);  
        }
        
        Mail::to($this->email_tujuan)->send(new SendEmail($this->modul, $pdf["pdf"], $this->lampiran)); 
    }
}
