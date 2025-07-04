<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $complaint;

    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function build()
    {
        return $this->view('emails.complaint_submitted')
            ->subject('New Complaint Submitted - #'.$this->complaint->id)
            ->with([
                'complaint' => $this->complaint,
                'student' => $this->complaint->student,
                'department' => $this->complaint->department,
            ]);
    }
}
