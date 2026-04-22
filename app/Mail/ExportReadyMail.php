<?php
// app/Mail/ExportReadyMail.php

namespace App\Mail;

use App\Models\ExportLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $export;
    
    public function __construct(ExportLog $export)
    {
        $this->export = $export;
    }
    
    public function build()
    {
        return $this->subject('Your Export is Ready')
                    ->view('emails.export-ready');
    }
}