<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Complaint;
use App\Notifications\ComplaintExportReady;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;

class ExportComplaintsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $user;

    public $tries = 1;
    public $timeout = 3600; // 1 hour

    public function __construct(array $filters, Admin $user)
    {
        $this->filters = $filters;
        $this->user = $user;
    }

    public function handle()
    {
        Log::info("Export Job Started (Final Batch Writing Method). User ID: {$this->user->id}");

        $fileName = 'complaints-report-' . $this->user->id . '-' . now()->timestamp . '.xlsx';
        $exportFolder = storage_path('app/public/exports');
        $filePath = $exportFolder . '/' . $fileName;

        if (!File::isDirectory($exportFolder)) {
            File::makeDirectory($exportFolder, 0755, true, true);
        }

        try {
            $query = $this->buildOptimizedQuery();
            $writer = new XLSXWriter();
            $writer->openToFile($filePath);

            $header = [
                'ID', 'Title', 'Description', 'Status', 'Department',
                'Student Name', 'Student Email', 'Submitted At',
            ];
            $writer->addRow(Row::fromValues($header));

            // =========================================================================
            // FINAL FIX IS IN THIS BLOCK
            // =========================================================================
            $query->chunkById(5000, function ($complaints) use ($writer) {
                
                // 1. Create a temporary array to hold all Row objects for this chunk.
                $rowsForChunk = [];

                foreach ($complaints as $complaint) {
                    // 2. Create a full Row object for each record and add it to our collection.
                    $rowsForChunk[] = Row::fromValues([
                        $complaint->id,
                        $complaint->title,
                        $complaint->description,
                        ucfirst($complaint->status),
                        $complaint->department_name ?? 'N/A',
                        $complaint->student_name ?? 'N/A',
                        $complaint->student_email ?? 'N/A',
                        $complaint->submitted_at,
                    ]);
                }

                // 3. Add all 5000 Row objects to the writer in a single, highly optimized operation.
                if (!empty($rowsForChunk)) {
                    $writer->addRows($rowsForChunk); // This now receives the correct data type.
                }

            }, 'c.id', 'id');

            $writer->close();
            Log::info("SUCCESS: Export file created at {$filePath}");

            // ... Notification logic is correct ...
            $recipientEmail = $this->user->Admin_email;
            $recipientName = $this->user->name ?? 'Admin';
            Notification::route('mail', $recipientEmail)
                ->notify(new ComplaintExportReady('exports/' . $fileName, $recipientName));
            Log::info("SUCCESS: Notification dispatched to {$recipientEmail}");

        } catch (\Exception $e) {
            Log::error('CRITICAL FAILURE IN EXPORT JOB: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
            $this->fail($e);
        }
    }
    
    // Your buildOptimizedQuery method is correct and does NOT need to be changed.
    protected function buildOptimizedQuery()
    {
        // =========================================================================
        // FINAL FIX: Using the correct table names 'students' and 'depts'
        // =========================================================================
        $query = DB::table('complaints as c')
            
            // This was correct, but we confirm it.
            ->leftJoin('students as s', 'c.student_id', '=', 's.id') 
            
            // This is the line that has been corrected.
            ->leftJoin('depts as d', 'c.Dept_id', '=', 'd.id') 
            
            ->select(
                'c.id',
                'c.title',
                'c.description',
                'c.status',
                DB::raw("DATE_FORMAT(c.created_at, '%Y-%m-%d %H:%i:%s') as submitted_at"),
                'd.Dept_name as department_name', // This selects from the 'depts' table
                's.Stud_name as student_name',   // This selects from the 'students' table
                's.Stud_email as student_email'
            );

        // Your filtering logic below this is perfect and does not need to change.
        $filters = $this->filters;
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('c.title', 'like', "%{$search}%")
                ->orWhere('s.Stud_name', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('c.status', $filters['status']);
        }
        if (!empty($filters['dept'])) {
            // This correctly filters on the complaints table, which is fast.
            $query->where('c.Dept_id', $filters['dept']);
        }
        if (!empty($filters['start_id']) && empty($filters['end_id'])) {
            $query->where('c.id', '>=', $filters['start_id']);
        }
        if (empty($filters['start_id']) && !empty($filters['end_id'])) {
            $query->where('c.id', '<=', $filters['end_id']);
        }
        if (!empty($filters['start_id']) && !empty($filters['end_id'])) {
            $query->whereBetween('c.id', [$filters['start_id'], $filters['end_id']]);
        }
        
        // Order by the aliased column for chunkById to work reliably
        return $query->orderBy('c.id');
    }
}
