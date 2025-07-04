<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    public function makecomplaint(Request $request)
    {
        Log::info('makecomplaint() initiated', ['student_id' => Auth::guard('students')->id(), 'payload' => $request->all()]);

        $validated = $request->validate([
            'department_id' => 'required|exists:depts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $complaint = Complaint::create([
            'Student_id' => Auth::guard('students')->id(),
            'Dept_id' => $validated['department_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        Log::info('Complaint created', ['complaint_id' => $complaint->id]);

        ActionLog::create([
            'user_type' => 'student',
            'user_id' => auth()->guard('students')->id(),
            'complaint_id' => $complaint->id,
            'action' => "Student submitted a complaint titled: $complaint->title",
        ]);

        Log::info('ActionLog created for complaint submission', ['complaint_id' => $complaint->id]);

        return redirect()->route('student.dashboard')->with('success', 'Complaint submitted!');
    }

    public function getComplaintCounts()
    {
        $studentId = auth()->guard('students')->id();
        Log::info('getComplaintCounts() called', ['student_id' => $studentId]);

        $total = Complaint::where('Student_id', $studentId)->count();
        $pending = Complaint::where('Student_id', $studentId)->where('status', 'pending')->count();
        $checking = Complaint::where('Student_id', $studentId)->where('status', 'checking')->count();
        $solved = Complaint::where('Student_id', $studentId)->where('status', 'solved')->count();
        $rejected = Complaint::where('Student_id', $studentId)->where('status', 'rejected')->count();
        $withdrawn = Complaint::where('Student_id', $studentId)->where('status', 'withdrawn')->count();

        Log::info('Complaint counts retrieved', [
            'student_id' => $studentId,
            'counts' => compact('total', 'pending', 'checking', 'solved', 'rejected', 'withdrawn'),
        ]);

        return response()->json([
            'total' => $total,
            'pending' => $pending,
            'checking' => $checking,
            'solved' => $solved,
            'rejected' => $rejected,
            'withdrawn' => $withdrawn,
        ]);
    }

    public function json(Request $request)
    {
        Log::info('json() called', ['filters' => $request->all()]);

        $query = Complaint::with(['department', 'student']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                ->orWhereHas('student', fn ($q) => $q->where('Stud_name', 'like', '%'.$request->search.'%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        Log::info('json() returning complaints', ['count' => $results->count()]);

        return response()->json(['data' => $results]);
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info('updateStatus() called', [
            'complaint_id' => $id,
            'new_status' => $request->status,
            'response' => $request->response,
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->response = $request->response;
        $complaint->save();

        Log::info('Complaint status updated', ['complaint_id' => $id]);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        Log::warning('delete() called', ['complaint_id' => $id]);

        Complaint::findOrFail($id)->delete();

        Log::info('Complaint deleted', ['complaint_id' => $id]);

        return redirect()->back()->with('success', 'Complaint deleted.');
    }
}
