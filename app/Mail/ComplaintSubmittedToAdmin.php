<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels; // <-- ADD THIS

class ComplaintSubmittedToAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function build()
    {
        return $this->subject("New Complaint Submitted: #{$this->complaint->id}")
            ->markdown('emails.complaints.submitted_to_admin');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // If there is an attachment path, attach the file from storage
        if ($this->complaint->attachment_path) {
            return [
                Attachment::fromStorageDisk('public', $this->complaint->attachment_path),
            ];
        }

        return []; // Return an empty array if no attachment
    }
}
