<?php

// In app/Observers/ComplaintObserver.php

namespace App\Observers;
use App\Models\Complaint;

class ComplaintObserver
{
    /**
     * Handle the Complaint "saving" event.
     * This ensures the priority is always in sync with the status.
     */
    public function saving(Complaint $complaint): void
    {
        // Set the priority based on the status string
        $complaint->status_priority = match ($complaint->status) {
            'pending'   => 1,
            'checking'  => 2,
            'solved'    => 3,
            'rejected'  => 4,
            'withdrawn' => 5,
            default     => 99,
        };
    }
}
