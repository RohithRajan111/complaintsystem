<?php

namespace App\Jobs;

use App\Mail\ComplaintSubmittedToAdmin;
use App\Models\Admin;
use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendComplaintSubmissionNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $complaint;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // 1. Send to the relevant department
            if ($this->complaint->department && $this->complaint->department->Dept_email) {
                Mail::to($this->complaint->department->Dept_email)
                    ->send(new ComplaintSubmittedToAdmin($this->complaint));
                Log::info("Complaint submission email sent to department: {$this->complaint->department->Dept_email}");
            }

            // 2. Send to all admins
            $admins = Admin::all(); // Assuming you have an Admin model
            foreach ($admins as $admin) {
                Mail::to($admin->Admin_email)->send(new ComplaintSubmittedToAdmin($this->complaint));
                Log::info("Complaint submission email sent to admin: {$admin->Admin_email}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send complaint submission email for complaint #{$this->complaint->id}: ".$e->getMessage());
        }
    }
}
