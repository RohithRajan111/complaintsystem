<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $complaint;

    public $oldStatus;

    public $newStatus;

    public function __construct(Complaint $complaint, $oldStatus, $newStatus)
    {
        $this->complaint = $complaint;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function build()
    {
        return $this->view('emails.complaint_status_updated')
            ->subject('Complaint Status Updated - #'.$this->complaint->id)
            ->with([
                'complaint' => $this->complaint,
                'student' => $this->complaint->student,
                'department' => $this->complaint->department,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
            ]);
    }
}
