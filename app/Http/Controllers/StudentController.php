<?php

namespace App\Http\Controllers;

use App\Jobs\SendComplaintSubmissionNotification;
use App\Models\ActionLog;
use App\Models\Complaint;
use App\Models\Dept;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    /**
     * These methods are for the old, separate login/register pages.
     * They can be removed once you are fully using the unified login system.
     */
    public function showregister()
    {
        return view('student.register');
    }

    public function showlogin()
    {
        return view('auth.login');
    }

    /**
     * Handles student registration.
     * LOGS: A new student account is created.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'Stud_name' => 'required|string|max:255',
            'Stud_email' => 'required|string|email|max:255|unique:students',
            'Password' => 'required|string|min:8|confirmed',
        ]);

        $student = Student::create([
            'Stud_name' => $validatedData['Stud_name'],
            'Stud_email' => $validatedData['Stud_email'],
            'password' => Hash::make($validatedData['Password']),
        ]);

        // --- ADDED ACTION LOG ---
        ActionLog::create([
            'user_type' => 'student',
            'user_id' => $student->id, // Use the ID of the newly created student
            'complaint_id' => null,
            'action' => "Student registered a new account: {$student->Stud_name} ({$student->Stud_email})",
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }

    public function dashboard()
    {
        $student = Auth::guard('web')->user();

        if (! $student) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        // --- START: MODIFIED SECTION ---
        // Get the base query for the student's complaints
        $complaintsQuery = $student->complaints();

        // Calculate stats for the cards
        $stats = [
            'active' => (clone $complaintsQuery)->whereIn('status', ['pending', 'checking'])->count(),
            'resolved' => (clone $complaintsQuery)->where('status', 'solved')->count(),
            'total' => (clone $complaintsQuery)->count(),
        ];

        // The main complaints list is loaded via AJAX, so we only need to pass the stats here.
        return view('student.dashboard', compact('student', 'stats'));
        // --- END: MODIFIED SECTION ---
    }

    public function ajaxMakeComplaintForm()
    {
        $departments = Dept::all();

        return view('student.partials.make_complaint_form', compact('departments'));
    }

    public function submitComplaint(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:depts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // Add validation for the attachment
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048', // max 2MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // Store the file in 'storage/app/public/attachments' and get its path
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        $complaint = Complaint::create([
            'Student_id' => Auth::guard('web')->id(),
            'Dept_id' => $validated['department_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $attachmentPath, // <-- Save the path to the DB
            'status' => 'pending',
        ]);

        $complaint->load(['student', 'department']);

        // Dispatch the job to send emails in the background
        SendComplaintSubmissionNotification::dispatch($complaint);

        ActionLog::create([
            'user_type' => 'student',
            'user_id' => Auth::guard('web')->id(),
            'complaint_id' => $complaint->id,
            'action' => "Student submitted a complaint titled: {$complaint->title}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complaint submitted successfully! The relevant parties have been notified.',
        ]);
    }

    public function ajaxMyComplaints()
    {
        $student = Auth::guard('web')->user();

        if (! $student) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $complaints = $student->complaints()->with(['department', 'responses'])->latest()->paginate(10);

        return view('student.partials.complaints_list', compact('complaints'));
    }

    public function withdrawComplaint(Request $request, $id)
    {
        $student = Auth::guard('web')->user();

        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Authentication required.'], 401);
        }

        $complaint = Complaint::where('id', $id)
            ->where('Student_id', $student->id)
            ->first();

        if (! $complaint) {
            return response()->json(['success' => false, 'message' => 'Complaint not found or you do not own it.'], 404);
        }

        if ($complaint->status === 'withdrawn') {
            return response()->json(['success' => false, 'message' => 'This complaint has already been withdrawn.'], 400);
        }

        $complaint->status = 'withdrawn';
        $complaint->save();

        ActionLog::create([
            'user_type' => 'student',
            'user_id' => $student->id,
            'complaint_id' => $complaint->id,
            'action' => "Student withdrew complaint: {$complaint->title}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complaint withdrawn successfully.',
        ]);
    }

    public function profile()
    {
        $student = Auth::guard('web')->user();

        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = Auth::guard('web')->user();

        if (! $student) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'Stud_name' => 'required|string|max:255',
            'Stud_email' => 'required|string|email|max:255|unique:students,Stud_email,'.$student->id,
        ]);

        $originalName = $student->Stud_name;
        $originalEmail = $student->Stud_email;
        $updated = $student->update($validated);

        if ($updated) {
            $logMessage = 'Student updated their profile.';
            if ($originalName !== $validated['Stud_name']) {
                $logMessage .= " Name changed from '{$originalName}' to '{$validated['Stud_name']}'.";
            }
            if ($originalEmail !== $validated['Stud_email']) {
                $logMessage .= " Email changed from '{$originalEmail}' to '{$validated['Stud_email']}'.";
            }

            // Only log if something actually changed
            if ($originalName !== $validated['Stud_name'] || $originalEmail !== $validated['Stud_email']) {
                ActionLog::create([
                    'user_type' => 'student',
                    'user_id' => $student->id,
                    'complaint_id' => null,
                    'action' => $logMessage,
                ]);
            }
        }

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}
