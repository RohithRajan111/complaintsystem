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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Show the student dashboard with complaints and statistics.
     * Handles search and filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dashboard(Request $request)
    {
        /** @var \App\Models\Student $student */
        $student = Auth::guard('web')->user();

        if (!$student) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        $search = $request->get('search');
        $statusFilter = $request->get('status');

        $complaintsQuery = $student->complaints()->with(['department', 'responses']);

        if ($search) {
            $complaintsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('department', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            });
        }

        if ($statusFilter) {
            $complaintsQuery->where('status', $statusFilter);
        }

        $complaints = $complaintsQuery->latest()->paginate(10);

        $statsQuery = $student->complaints();
        $stats = [
            'active' => (clone $statsQuery)->whereIn('status', ['pending', 'checking'])->count(),
            'resolved' => (clone $statsQuery)->where('status', 'solved')->count(),
            'total' => (clone $statsQuery)->count(),
        ];

        return view('student.dashboard', compact('student', 'stats', 'complaints'));
    }

    /**
     * Load the complaint form via AJAX.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function ajaxMakeComplaintForm()
    {
        try {
            $departments = Dept::orderBy('Dept_name')->get();

            return view('student.partials.make_complaint_form', compact('departments'));
        } catch (\Exception $e) {
            Log::error('Error loading complaint form: ' . $e->getMessage());
            return response()->json(['message' => 'Unable to load complaint form. Please refresh the page and try again.'], 500);
        }
    }

    /**
     * Submit a new complaint via AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitComplaint(Request $request)
    {
        try {
            $validated = $request->validate([
                'department_id' => 'required|exists:depts,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
            ], [
                'department_id.required' => 'Please select a department.',
                'department_id.exists' => 'Selected department is invalid.',
                'title.required' => 'Please provide a title for your complaint.',
                'title.max' => 'Title cannot exceed 255 characters.',
                'description.required' => 'Please provide a description for your complaint.',
                'description.max' => 'Description cannot exceed 2000 characters.',
                'attachment.mimes' => 'Attachment must be a JPG, JPEG, PNG, PDF, or DOCX file.',
                'attachment.max' => 'Attachment size cannot exceed 2MB.',
            ]);

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('attachments', $filename, 'public');
            }

            $complaint = Complaint::create([
                'Student_id' => Auth::guard('web')->id(),
                'Dept_id' => $validated['department_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'attachment_path' => $attachmentPath,
                'status' => 'pending',
            ]);

            $complaint->load(['student', 'department']);

            if (class_exists('App\Jobs\SendComplaintSubmissionNotification')) {
                SendComplaintSubmissionNotification::dispatch($complaint);
            }

        
            ActionLog::create([
                'user_type' => 'student',
                'user_id' => Auth::guard('web')->id(),
                'complaint_id' => $complaint->id,
                'action' => "Student submitted a complaint titled: {$complaint->title}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Complaint submitted successfully! The relevant department has been notified.',
                'complaint_id' => $complaint->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error submitting complaint: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while submitting your complaint. Please try again.',
            ], 500);
        }
    }

    /**
     * Withdraw a complaint via AJAX.
     *
     * @param  int  $id The ID of the complaint to withdraw.
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawComplaint($id)
    {
        try {
            /** @var \App\Models\Student $student */
            $student = Auth::guard('web')->user();

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Authentication required.'], 401);
            }

            $complaint = Complaint::where('id', $id)
                ->where('Student_id', $student->id)
                ->first();

            if (!$complaint) {
                return response()->json(['success' => false, 'message' => 'Complaint not found or you do not have permission to modify it.'], 404);
            }

            if (!in_array($complaint->status, ['pending', 'checking'])) {
                return response()->json(['success' => false, 'message' => 'This complaint cannot be withdrawn as it is already resolved or closed.'], 400);
            }

            $complaint->status = 'withdrawn';
            $complaint->save();

            ActionLog::create([
                'user_type' => 'student',
                'user_id' => $student->id,
                'complaint_id' => $complaint->id,
                'action' => "Student withdrew the complaint titled: {$complaint->title}",
            ]);

            return response()->json(['success' => true, 'message' => 'Complaint has been successfully withdrawn.']);

        } catch (\Exception $e) {
            Log::error("Error withdrawing complaint #{$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    /**
     * Show the student's profile page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function profile()
    {
        $student = Auth::guard('web')->user();
        if (!$student) {
            return redirect()->route('login');
        }
        return view('student.profile', compact('student'));
    }

    /**
     * Update the student's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Student $student */
        $student = Auth::guard('web')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('students')->ignore($student->id),
            ],
            'current_password' => 'nullable|string|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $student->password)) {
                return back()->withErrors(['current_password' => 'The provided current password does not match our records.'])->withInput();
            }
            $student->password = Hash::make($request->new_password);
        }

        $student->name = $request->name;
        $student->email = $request->email;
        $student->save();

        return redirect()->route('student.profile')->with('success', 'Your profile has been updated successfully.');
    }
}