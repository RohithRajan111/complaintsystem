<?php

namespace App\Mail;

use App\Models\Complaint;
use App\Models\Complaint_Response;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintResponseReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $complaint;

    public $response;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Complaint $complaint, Complaint_Response $response)
    {
        $this->complaint = $complaint;
        $this->response = $response;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Update on your Complaint: #{$this->complaint->id}")
            ->markdown('emails.complaints.response_received');
    }
}
