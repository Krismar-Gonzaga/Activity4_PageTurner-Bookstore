<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BackupStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        $subject = $this->details['status'] === 'failed' 
            ? 'Backup Failed - Admin Notification' 
            : 'Backup Completed - Weekly Summary';

        return $this->subject($subject)
            ->view('emails.backup-status')
            ->with('details', $this->details);
    }
}