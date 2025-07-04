<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileName;

    public $downloadUrl;

    /**
     * Create a new message instance.
     *
     * @param  string  $relativePath  The path to the file within the public storage disk (e.g., 'exports/report.xlsx')
     */
    public function __construct(string $relativePath)
    {
        // Get just the filename part for display in the email
        $this->fileName = basename($relativePath);

        // Generate the full, public URL for the download button
        $this->downloadUrl = asset('storage/'.$relativePath);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Complaint Report Export is Ready',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.exports.ready',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
