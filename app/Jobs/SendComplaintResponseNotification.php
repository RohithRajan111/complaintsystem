<?php

namespace App\Jobs;

use App\Mail\ComplaintResponseReceived;
use App\Models\Complaint;
use App\Models\Complaint_Response;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendComplaintResponseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $complaint;

    protected $response;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Complaint $complaint, Complaint_Response $response)
    {
        $this->complaint = $complaint;
        $this->response = $response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->complaint->student->Stud_email)
                ->send(new ComplaintResponseReceived($this->complaint, $this->response));
            Log::info("Complaint response email sent to student: {$this->complaint->student->Stud_email}");
        } catch (\Exception $e) {
            Log::error("Failed to send complaint response email for complaint #{$this->complaint->id}: ".$e->getMessage());
        }
    }
}
