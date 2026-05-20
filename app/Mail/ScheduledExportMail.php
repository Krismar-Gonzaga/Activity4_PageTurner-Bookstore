<?php

namespace App\Mail;

use App\Models\ScheduledExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ScheduledExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduledExport;
    public $generatedAt;
    protected $filePath;

    public function __construct(ScheduledExport $scheduledExport, string $filePath)
    {
        $this->scheduledExport = $scheduledExport;
        $this->filePath = $filePath;
        $this->generatedAt = now()->format('F j, Y g:i A');
    }

    public function build()
    {
        $mail = $this->subject("Scheduled Export: {$this->scheduledExport->name}")
            ->view('emails.scheduled-export');

        if (File::exists($this->filePath)) {
            $mail->attach($this->filePath);
        }

        return $mail;
    }
}
